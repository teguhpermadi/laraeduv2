<?php

namespace App\Services;

use App\Enums\CategoryLegerEnum;
use App\Models\ReportDescription;

class DescriptionService
{
    public function getDescription($data, ?ReportDescription $template = null): array
    {
        $filter = collect($data)->reject(function ($item) {
            $code = strtolower($item['competency']['code']);

            return $code === CategoryLegerEnum::HALF_SEMESTER->value || $code === CategoryLegerEnum::FULL_SEMESTER->value;
        })->values();

        $student = $data->first()->student;

        $passedMaterials = [];
        $notPassedMaterials = [];
        $passedSkills = [];
        $notPassedSkills = [];
        $allScores = [];
        $allSkillScores = [];

        foreach ($filter as $competency) {
            $score = $competency->score;
            $allScores[] = $score;

            if ($score >= $competency->competency->passing_grade) {
                $passedMaterials[] = $competency->competency->description;
            } else {
                $notPassedMaterials[] = $competency->competency->description;
            }

            if ($competency->score_skill) {
                $skillScore = $competency->score_skill;
                $allSkillScores[] = $skillScore;

                if ($skillScore >= $competency->competency->passing_grade) {
                    $passedSkills[] = $competency->competency->description_skill;
                } else {
                    $notPassedSkills[] = $competency->competency->description_skill;
                }
            }
        }

        $countPassed = count($passedMaterials);
        $countNotPassed = count($notPassedMaterials);
        $countPassedSkill = count($passedSkills);
        $countNotPassedSkill = count($notPassedSkills);

        $highestScore = ! empty($allScores) ? max($allScores) : null;
        $lowestScore = ! empty($allScores) ? min($allScores) : null;
        $averageScore = ! empty($allScores) ? round(array_sum($allScores) / count($allScores), 1) : null;

        $highestScoreName = null;
        $lowestScoreName = null;
        if ($highestScore !== null) {
            foreach ($filter as $c) {
                if ((int) $c->score === (int) $highestScore) {
                    $highestScoreName = $c->competency->description;
                    break;
                }
            }
        }
        if ($lowestScore !== null) {
            foreach ($filter as $c) {
                if ((int) $c->score === (int) $lowestScore) {
                    $lowestScoreName = $c->competency->description;
                    break;
                }
            }
        }

        $highestSkill = ! empty($allSkillScores) ? max($allSkillScores) : null;
        $lowestSkill = ! empty($allSkillScores) ? min($allSkillScores) : null;
        $averageSkill = ! empty($allSkillScores) ? round(array_sum($allSkillScores) / count($allSkillScores), 1) : null;

        $highestSkillName = null;
        $lowestSkillName = null;
        if ($highestSkill !== null) {
            foreach ($filter as $c) {
                if ((int) $c->score_skill === (int) $highestSkill) {
                    $highestSkillName = $c->competency->description_skill;
                    break;
                }
            }
        }
        if ($lowestSkill !== null) {
            foreach ($filter as $c) {
                if ((int) $c->score_skill === (int) $lowestSkill) {
                    $lowestSkillName = $c->competency->description_skill;
                    break;
                }
            }
        }

        $passingGrade = $filter->first()->competency->passing_grade ?? null;

        $replacements = [
            '{student_name}' => $student->name,
            '{student_nickname}' => $student->nick_name ?? $student->name,
            '{materials_passed}' => implode('; ', $passedMaterials),
            '{materials_not_passed}' => implode('; ', $notPassedMaterials),
            '{count_passed}' => $countPassed,
            '{count_not_passed}' => $countNotPassed,
            '{highest_score}' => $highestScore,
            '{highest_score_name}' => $highestScoreName,
            '{lowest_score}' => $lowestScore,
            '{lowest_score_name}' => $lowestScoreName,
            '{average_score}' => $averageScore,
            '{passing_grade}' => $passingGrade,
            '{skills_passed}' => implode('; ', $passedSkills),
            '{skills_not_passed}' => implode('; ', $notPassedSkills),
            '{count_skill_passed}' => $countPassedSkill,
            '{count_skill_not_passed}' => $countNotPassedSkill,
            '{highest_skill}' => $highestSkill,
            '{highest_skill_name}' => $highestSkillName,
            '{lowest_skill}' => $lowestSkill,
            '{lowest_skill_name}' => $lowestSkillName,
            '{average_skill}' => $averageSkill,
        ];

        $knowledgeTemplate = $template?->knowledge_template;
        $skillTemplate = $template?->skill_template;

        if (blank($knowledgeTemplate)) {
            $knowledgeTemplate = 'Alhamdulillah, ananda {student_name}{if_passed} telah menguasai materi: {materials_passed}{/if_passed}{if_not_passed} tetapi masih perlu peningkatan lagi pada materi: {materials_not_passed}{/if_not_passed}';
        }

        $description = $this->parseTemplate($knowledgeTemplate, $replacements, [
            'if_passed' => $countPassed > 0,
            'if_not_passed' => $countNotPassed > 0,
        ]);

        $hasSkills = $countPassedSkill > 0 || $countNotPassedSkill > 0;
        $descriptionSkill = null;

        if ($hasSkills) {
            if (blank($skillTemplate)) {
                $skillTemplate = 'Alhamdulillah, ananda {student_name}{if_skill_passed} telah menguasai keterampilan: {skills_passed}{/if_skill_passed}{if_skill_not_passed} tetapi masih perlu peningkatan lagi pada keterampilan: {skills_not_passed}{/if_skill_not_passed}';
            }

            $descriptionSkill = $this->parseTemplate($skillTemplate, $replacements, [
                'if_skill_passed' => $countPassedSkill > 0,
                'if_skill_not_passed' => $countNotPassedSkill > 0,
            ]);
        }

        return [
            'description' => $description,
            'description_skill' => $descriptionSkill,
        ];
    }

    public function previewTemplate(?string $template, string $type): string
    {
        $dummy = [
            '{student_name}' => 'Ahmad Rizki',
            '{student_nickname}' => 'Rizki',
            '{materials_passed}' => 'Menulis puisi; Membaca cepat; Berhitung',
            '{materials_not_passed}' => 'Menyimak; Berbicara',
            '{count_passed}' => 3,
            '{count_not_passed}' => 2,
            '{highest_score}' => 90,
            '{highest_score_name}' => 'Membaca cepat',
            '{lowest_score}' => 65,
            '{lowest_score_name}' => 'Menyimak',
            '{average_score}' => 78.5,
            '{passing_grade}' => 70,
            '{skills_passed}' => 'Praktik membaca; Praktik menulis',
            '{skills_not_passed}' => 'Praktik menyimak',
            '{count_skill_passed}' => 2,
            '{count_skill_not_passed}' => 1,
            '{highest_skill}' => 88,
            '{highest_skill_name}' => 'Praktik membaca',
            '{lowest_skill}' => 70,
            '{lowest_skill_name}' => 'Praktik menyimak',
            '{average_skill}' => 80,
        ];

        if (blank($template)) {
            if ($type === 'knowledge') {
                $template = 'Alhamdulillah, ananda {student_name}{if_passed} telah menguasai materi: {materials_passed}{/if_passed}{if_not_passed} tetapi masih perlu peningkatan lagi pada materi: {materials_not_passed}{/if_not_passed}';
            } elseif ($type === 'skill') {
                $template = 'Alhamdulillah, ananda {student_name}{if_skill_passed} telah menguasai keterampilan: {skills_passed}{/if_skill_passed}{if_skill_not_passed} tetapi masih perlu peningkatan lagi pada keterampilan: {skills_not_passed}{/if_skill_not_passed}';
            }
        }

        $isKnowledge = $type === 'knowledge';
        $conditions = $isKnowledge
            ? ['if_passed' => true, 'if_not_passed' => true]
            : ['if_skill_passed' => true, 'if_skill_not_passed' => true];

        return $this->parseTemplate($template, $dummy, $conditions);
    }

    public function validateTemplate(?string $template, string $type): array
    {
        $errors = [];
        $warnings = [];

        if (blank($template)) {
            return ['valid' => true, 'errors' => [], 'warnings' => []];
        }

        $validConditionals = $type === 'knowledge'
            ? ['if_passed', 'if_not_passed']
            : ['if_skill_passed', 'if_skill_not_passed'];

        $validPlaceholders = $type === 'knowledge'
            ? ['{student_name}', '{student_nickname}', '{materials_passed}', '{materials_not_passed}', '{count_passed}', '{count_not_passed}', '{highest_score}', '{highest_score_name}', '{lowest_score}', '{lowest_score_name}', '{average_score}', '{passing_grade}']
            : ['{student_name}', '{student_nickname}', '{skills_passed}', '{skills_not_passed}', '{count_skill_passed}', '{count_skill_not_passed}', '{highest_skill}', '{highest_skill_name}', '{lowest_skill}', '{lowest_skill_name}', '{average_skill}', '{passing_grade}'];

        // Check balanced conditional blocks
        foreach ($validConditionals as $cond) {
            $openCount = preg_match_all('/\{'.preg_quote($cond, '/').'\}/', $template);
            $closeCount = preg_match_all('/\{\/'.preg_quote($cond, '/').'\}/', $template);

            if ($openCount !== $closeCount) {
                $errors[] = "Block {{$cond}} tidak seimbang (buka: {$openCount}, tutup: {$closeCount}).";
            }
        }

        // Detect unknown placeholders
        $allValid = array_merge($validPlaceholders, array_map(fn ($c) => '{'.$c.'}', $validConditionals));
        $allValid[] = '{/if_passed}';
        $allValid[] = '{/if_not_passed}';
        $allValid[] = '{/if_skill_passed}';
        $allValid[] = '{/if_skill_not_passed}';

        preg_match_all('/\{[^}]+\}/', $template, $matches);
        foreach ($matches[0] as $match) {
            if (! in_array($match, $allValid)) {
                $warnings[] = "Placeholder '{$match}' tidak dikenal.";
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings,
        ];
    }

    private function parseTemplate(string $template, array $replacements, array $conditions): string
    {
        foreach ($conditions as $condition => $isTrue) {
            $pattern = '/\{'.preg_quote($condition, '/').'\}(.*?)\{\/'.preg_quote($condition, '/').'\}/s';
            $template = preg_replace_callback($pattern, function ($matches) use ($isTrue) {
                return $isTrue ? $matches[1] : '';
            }, $template);
        }

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }
}
