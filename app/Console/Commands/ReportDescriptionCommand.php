<?php

namespace App\Console\Commands;

use App\Models\AcademicYear;
use App\Models\ReportDescription;
use Illuminate\Console\Command;

class ReportDescriptionCommand extends Command
{
    protected $signature = 'report:description
                           {academic_year_id? : ID tahun akademik (ULID)}
                           {--show : Lihat template saat ini}
                           {--set-knowledge= : Set template pengetahuan}
                           {--set-skill= : Set template keterampilan}
                           {--reset : Reset ke template bawaan (hapus record)}
                           {--list : Tampilkan daftar placeholder yang tersedia}';

    protected $description = 'Kelola template deskripsi rapor per tahun akademik';

    private array $knowledgePlaceholders = [
        '{student_name}' => 'Nama lengkap siswa',
        '{student_nickname}' => 'Nama panggilan siswa',
        '{materials_passed}' => 'Daftar materi yang lulus',
        '{materials_not_passed}' => 'Daftar materi tidak lulus',
        '{count_passed}' => 'Jumlah materi lulus',
        '{count_not_passed}' => 'Jumlah materi tidak lulus',
        '{highest_score}' => 'Nilai tertinggi',
        '{highest_score_name}' => 'Nama materi nilai tertinggi',
        '{lowest_score}' => 'Nilai terendah',
        '{lowest_score_name}' => 'Nama materi nilai terendah',
        '{average_score}' => 'Rata-rata nilai',
        '{passing_grade}' => 'KKM',
    ];

    private array $skillPlaceholders = [
        '{student_name}' => 'Nama lengkap siswa',
        '{student_nickname}' => 'Nama panggilan siswa',
        '{skills_passed}' => 'Daftar keterampilan lulus',
        '{skills_not_passed}' => 'Daftar keterampilan tidak lulus',
        '{count_skill_passed}' => 'Jumlah keterampilan lulus',
        '{count_skill_not_passed}' => 'Jumlah keterampilan tidak lulus',
        '{highest_skill}' => 'Nilai keterampilan tertinggi',
        '{highest_skill_name}' => 'Nama keterampilan nilai tertinggi',
        '{lowest_skill}' => 'Nilai keterampilan terendah',
        '{lowest_skill_name}' => 'Nama keterampilan nilai terendah',
        '{average_skill}' => 'Rata-rata nilai keterampilan',
        '{passing_grade}' => 'KKM',
    ];

    private array $knowledgeConditionals = [
        '{if_passed}...{/if_passed}' => 'Hanya muncul jika ada materi lulus',
        '{if_not_passed}...{/if_not_passed}' => 'Hanya muncul jika ada materi tidak lulus',
    ];

    private array $skillConditionals = [
        '{if_skill_passed}...{/if_skill_passed}' => 'Hanya muncul jika ada keterampilan lulus',
        '{if_skill_not_passed}...{/if_skill_not_passed}' => 'Hanya muncul jika ada keterampilan tidak lulus',
    ];

    public function handle()
    {
        if ($this->option('list')) {
            $this->displayPlaceholders();

            return Command::SUCCESS;
        }

        $academicYearId = $this->argument('academic_year_id');

        if (! $academicYearId) {
            $academicYearId = $this->askForAcademicYear();
        }

        $academicYear = AcademicYear::find($academicYearId);

        if (! $academicYear) {
            $this->error("Tahun akademik dengan ID {$academicYearId} tidak ditemukan.");

            return Command::FAILURE;
        }

        $this->info("Tahun Akademik: {$academicYear->year} - {$academicYear->semester}");
        $this->newLine();

        if ($this->option('reset')) {
            return $this->resetTemplate($academicYear);
        }

        if ($this->option('set-knowledge')) {
            return $this->setKnowledgeTemplate($academicYear, $this->option('set-knowledge'));
        }

        if ($this->option('set-skill')) {
            return $this->setSkillTemplate($academicYear, $this->option('set-skill'));
        }

        if ($this->option('show') || ! $this->hasOptionChanges()) {
            $this->showTemplate($academicYear);

            return Command::SUCCESS;
        }

        $this->line('Gunakan --show untuk melihat, --set-knowledge / --set-skill untuk mengubah, --reset untuk reset, atau --list untuk daftar placeholder.');
        $this->line('Contoh: php artisan report:description <id> --set-knowledge="Alhamdulillah, ananda {student_name}..."');

        return Command::SUCCESS;
    }

    private function displayPlaceholders(): void
    {
        $this->info('=== PLACEHOLDER PENGETAHUAN ===');
        $this->table(['Placeholder', 'Keterangan'], collect($this->knowledgePlaceholders)->map(fn ($desc, $key) => [$key, $desc])->values()->toArray());

        $this->newLine();
        $this->info('Block Bersyarat (Pengetahuan):');
        $this->table(['Block', 'Keterangan'], collect($this->knowledgeConditionals)->map(fn ($desc, $key) => [$key, $desc])->values()->toArray());

        $this->newLine();
        $this->info('=== PLACEHOLDER KETERAMPILAN ===');
        $this->table(['Placeholder', 'Keterangan'], collect($this->skillPlaceholders)->map(fn ($desc, $key) => [$key, $desc])->values()->toArray());

        $this->newLine();
        $this->info('Block Bersyarat (Keterampilan):');
        $this->table(['Block', 'Keterangan'], collect($this->skillConditionals)->map(fn ($desc, $key) => [$key, $desc])->values()->toArray());

        $this->newLine();
        $this->info('Contoh Template Pengetahuan:');
        $this->line('Alhamdulillah, ananda {student_name}{if_passed} telah menguasai materi: {materials_passed}{/if_passed}{if_not_passed} tetapi masih perlu peningkatan lagi pada materi: {materials_not_passed}{/if_not_passed}');

        $this->newLine();
        $this->info('Contoh Template Keterampilan:');
        $this->line('Alhamdulillah, ananda {student_name}{if_skill_passed} telah menguasai keterampilan: {skills_passed}{/if_skill_passed}{if_skill_not_passed} tetapi masih perlu peningkatan lagi pada keterampilan: {skills_not_passed}{/if_skill_not_passed}');
    }

    private function showTemplate(AcademicYear $academicYear): void
    {
        $desc = $academicYear->reportDescription;

        $this->info('--- TEMPLATE PENGETAHUAN ---');
        if ($desc && $desc->knowledge_template) {
            $this->line($desc->knowledge_template);
        } else {
            $this->line('<comment>Menggunakan template bawaan</comment>');
            $this->line('Alhamdulillah, ananda {student_name}{if_passed} telah menguasai materi: {materials_passed}{/if_passed}{if_not_passed} tetapi masih perlu peningkatan lagi pada materi: {materials_not_passed}{/if_not_passed}');
        }

        $this->newLine();
        $this->info('--- TEMPLATE KETERAMPILAN ---');
        if ($desc && $desc->skill_template) {
            $this->line($desc->skill_template);
        } elseif ($desc === null) {
            $this->line('<comment>Belum ada data. Biarkan kosong untuk menggunakan template bawaan.</comment>');
        } else {
            $this->line('<comment>Menggunakan template bawaan</comment>');
            $this->line('Alhamdulillah, ananda {student_name}{if_skill_passed} telah menguasai keterampilan: {skills_passed}{/if_skill_passed}{if_skill_not_passed} tetapi masih perlu peningkatan lagi pada keterampilan: {skills_not_passed}{/if_skill_not_passed}');
        }
    }

    private function setKnowledgeTemplate(AcademicYear $academicYear, string $template): int
    {
        ReportDescription::updateOrCreate(
            ['academic_year_id' => $academicYear->id],
            ['knowledge_template' => $template]
        );

        $this->info('Template pengetahuan berhasil disimpan.');

        return Command::SUCCESS;
    }

    private function setSkillTemplate(AcademicYear $academicYear, string $template): int
    {
        ReportDescription::updateOrCreate(
            ['academic_year_id' => $academicYear->id],
            ['skill_template' => $template]
        );

        $this->info('Template keterampilan berhasil disimpan.');

        return Command::SUCCESS;
    }

    private function resetTemplate(AcademicYear $academicYear): int
    {
        if ($academicYear->reportDescription) {
            $academicYear->reportDescription->delete();
            $this->info('Template deskripsi berhasil di-reset ke bawaan sistem.');
        } else {
            $this->info('Tidak ada template kustom, sudah menggunakan bawaan sistem.');
        }

        return Command::SUCCESS;
    }

    private function askForAcademicYear(): string
    {
        $years = AcademicYear::all();
        $choices = $years->mapWithKeys(fn ($y) => [$y->id => "{$y->year} - {$y->semester} ({$y->id})"])->toArray();

        return $this->choice('Pilih tahun akademik', $choices);
    }

    private function hasOptionChanges(): bool
    {
        return $this->option('show')
            || $this->option('set-knowledge')
            || $this->option('set-skill')
            || $this->option('reset');
    }
}
