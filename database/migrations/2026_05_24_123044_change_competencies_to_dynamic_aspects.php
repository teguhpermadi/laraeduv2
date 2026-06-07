<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop foreign key constraints on the old tables before renaming
        Schema::table('student_competencies', function (Blueprint $table) {
            $table->dropForeign(['competency_id']);
            $table->dropForeign(['student_id']);
            $table->dropForeign(['teacher_subject_id']);
        });

        Schema::table('competencies', function (Blueprint $table) {
            $table->dropForeign(['teacher_subject_id']);
        });

        // 2. Rename existing tables
        Schema::rename('competencies', 'competencies_old');
        Schema::rename('student_competencies', 'student_competencies_old');
        // Disable foreign key constraints before creating new tables
        Schema::disableForeignKeyConstraints();
        // 3. Create new competencies table
        Schema::create('competencies', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->foreignUlid('teacher_subject_id')->constrained('teacher_subjects')->cascadeOnDelete();
            $table->string('code');
            $table->text('description');
            $table->string('aspect')->default('knowledge'); // using 'knowledge' or 'skill'
            $table->integer('passing_grade')->default(70);
            $table->boolean('half_semester')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // 4. Create new student_competencies table
        Schema::create('student_competencies', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->foreignUlid('teacher_subject_id')->constrained('teacher_subjects')->cascadeOnDelete();
            $table->foreignUlid('competency_id')->constrained('competencies')->cascadeOnDelete();
            $table->foreignUlid('student_id')->constrained('students')->cascadeOnDelete();
            $table->integer('score')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->unique(['student_id', 'competency_id', 'teacher_subject_id'], 'student_competency_unique');
        });

        // 5. Data Migration (ETL)
        $oldCompetencies = DB::table('competencies_old')->get();
        $idMap = [];

        foreach ($oldCompetencies as $old) {
            // A. Knowledge aspect
            if (!empty($old->code) || !empty($old->description)) {
                $newKnowledgeId = (string) Str::ulid();
                DB::table('competencies')->insert([
                    'id' => $newKnowledgeId,
                    'teacher_subject_id' => $old->teacher_subject_id,
                    'code' => $old->code ?? 'TP',
                    'description' => $old->description ?? '',
                    'aspect' => 'knowledge',
                    'passing_grade' => $old->passing_grade ?? 70,
                    'half_semester' => $old->half_semester ?? false,
                    'created_at' => $old->created_at ?? now(),
                    'updated_at' => $old->updated_at ?? now(),
                ]);
                $idMap[$old->id]['knowledge'] = $newKnowledgeId;
            }

            // B. Skill aspect
            if (!empty($old->code_skill) || !empty($old->description_skill)) {
                $newSkillId = (string) Str::ulid();
                DB::table('competencies')->insert([
                    'id' => $newSkillId,
                    'teacher_subject_id' => $old->teacher_subject_id,
                    'code' => $old->code_skill ?? 'TP-K',
                    'description' => $old->description_skill ?? '',
                    'aspect' => 'skill',
                    'passing_grade' => $old->passing_grade ?? 70,
                    'half_semester' => $old->half_semester ?? false,
                    'created_at' => $old->created_at ?? now(),
                    'updated_at' => $old->updated_at ?? now(),
                ]);
                $idMap[$old->id]['skill'] = $newSkillId;
            }
        }

        // ETL student_competencies_old to student_competencies
        $oldStudentCompetencies = DB::table('student_competencies_old')->get();

        foreach ($oldStudentCompetencies as $oldSc) {
            // Knowledge score
            if (isset($idMap[$oldSc->competency_id]['knowledge'])) {
                DB::table('student_competencies')->insert([
                    'id' => (string) Str::ulid(),
                    'teacher_subject_id' => $oldSc->teacher_subject_id,
                    'competency_id' => $idMap[$oldSc->competency_id]['knowledge'],
                    'student_id' => $oldSc->student_id,
                    'score' => $oldSc->score ?? 0,
                    'created_at' => $oldSc->created_at ?? now(),
                    'updated_at' => $oldSc->updated_at ?? now(),
                ]);
            }

            // Skill score
            if (isset($idMap[$oldSc->competency_id]['skill'])) {
                DB::table('student_competencies')->insert([
                    'id' => (string) Str::ulid(),
                    'teacher_subject_id' => $oldSc->teacher_subject_id,
                    'competency_id' => $idMap[$oldSc->competency_id]['skill'],
                    'student_id' => $oldSc->student_id,
                    'score' => $oldSc->score_skill ?? 0,
                    'created_at' => $oldSc->created_at ?? now(),
                    'updated_at' => $oldSc->updated_at ?? now(),
                ]);
            }
        }

        // 6. Drop old backup tables
        Schema::dropIfExists('student_competencies_old');
        Schema::dropIfExists('competencies_old');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // To rollback, we recreate the old structures and delete the new ones
        Schema::dropIfExists('student_competencies');
        Schema::dropIfExists('competencies');

        Schema::create('competencies', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->foreignUlid('teacher_subject_id')->constrained('teacher_subjects')->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->string('passing_grade')->default(0);
            $table->boolean('half_semester')->default(false);
            $table->string('code_skill')->nullable();
            $table->text('description_skill')->nullable();
            $table->timestamps();
        });

        Schema::create('student_competencies', function (Blueprint $table) {
            $table->ulid('id')->primary()->unique();
            $table->foreignUlid('teacher_subject_id')->constrained('teacher_subjects')->cascadeOnDelete();
            $table->foreignUlid('competency_id')->constrained('competencies')->cascadeOnDelete();
            $table->foreignUlid('student_id')->constrained('students')->cascadeOnDelete();
            $table->integer('score')->default(0);
            $table->integer('score_skill')->default(0);
            $table->timestamps();
            
            $table->unique(['student_id', 'competency_id', 'teacher_subject_id'], 'student_competency_unique');
        });
    }
};
