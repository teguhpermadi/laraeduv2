<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '
        [
            {
                "name": "super_admin",
                "guard_name": "web",
                "permissions": [
                    "view_academic::year",
                    "view_any_academic::year",
                    "create_academic::year",
                    "update_academic::year",
                    "restore_academic::year",
                    "restore_any_academic::year",
                    "replicate_academic::year",
                    "reorder_academic::year",
                    "delete_academic::year",
                    "delete_any_academic::year",
                    "force_delete_academic::year",
                    "force_delete_any_academic::year",
                    "view_activitylog",
                    "view_any_activitylog",
                    "create_activitylog",
                    "update_activitylog",
                    "restore_activitylog",
                    "restore_any_activitylog",
                    "replicate_activitylog",
                    "reorder_activitylog",
                    "delete_activitylog",
                    "delete_any_activitylog",
                    "force_delete_activitylog",
                    "force_delete_any_activitylog",
                    "view_attendance",
                    "view_any_attendance",
                    "create_attendance",
                    "update_attendance",
                    "restore_attendance",
                    "restore_any_attendance",
                    "replicate_attendance",
                    "reorder_attendance",
                    "delete_attendance",
                    "delete_any_attendance",
                    "force_delete_attendance",
                    "force_delete_any_attendance",
                    "view_attitude",
                    "view_any_attitude",
                    "create_attitude",
                    "update_attitude",
                    "restore_attitude",
                    "restore_any_attitude",
                    "replicate_attitude",
                    "reorder_attitude",
                    "delete_attitude",
                    "delete_any_attitude",
                    "force_delete_attitude",
                    "force_delete_any_attitude",
                    "view_competency",
                    "view_any_competency",
                    "create_competency",
                    "update_competency",
                    "restore_competency",
                    "restore_any_competency",
                    "replicate_competency",
                    "reorder_competency",
                    "delete_competency",
                    "delete_any_competency",
                    "force_delete_competency",
                    "force_delete_any_competency",
                    "view_competency::quran",
                    "view_any_competency::quran",
                    "create_competency::quran",
                    "update_competency::quran",
                    "restore_competency::quran",
                    "restore_any_competency::quran",
                    "replicate_competency::quran",
                    "reorder_competency::quran",
                    "delete_competency::quran",
                    "delete_any_competency::quran",
                    "force_delete_competency::quran",
                    "force_delete_any_competency::quran",
                    "view_extracurricular",
                    "view_any_extracurricular",
                    "create_extracurricular",
                    "update_extracurricular",
                    "restore_extracurricular",
                    "restore_any_extracurricular",
                    "replicate_extracurricular",
                    "reorder_extracurricular",
                    "delete_extracurricular",
                    "delete_any_extracurricular",
                    "force_delete_extracurricular",
                    "force_delete_any_extracurricular",
                    "view_grade",
                    "view_any_grade",
                    "create_grade",
                    "update_grade",
                    "restore_grade",
                    "restore_any_grade",
                    "replicate_grade",
                    "reorder_grade",
                    "delete_grade",
                    "delete_any_grade",
                    "force_delete_grade",
                    "force_delete_any_grade",
                    "view_project",
                    "view_any_project",
                    "create_project",
                    "update_project",
                    "restore_project",
                    "restore_any_project",
                    "replicate_project",
                    "reorder_project",
                    "delete_project",
                    "delete_any_project",
                    "force_delete_project",
                    "force_delete_any_project",
                    "view_project::coordinator",
                    "view_any_project::coordinator",
                    "create_project::coordinator",
                    "update_project::coordinator",
                    "restore_project::coordinator",
                    "restore_any_project::coordinator",
                    "replicate_project::coordinator",
                    "reorder_project::coordinator",
                    "delete_project::coordinator",
                    "delete_any_project::coordinator",
                    "force_delete_project::coordinator",
                    "force_delete_any_project::coordinator",
                    "view_quran::grade",
                    "view_any_quran::grade",
                    "create_quran::grade",
                    "update_quran::grade",
                    "restore_quran::grade",
                    "restore_any_quran::grade",
                    "replicate_quran::grade",
                    "reorder_quran::grade",
                    "delete_quran::grade",
                    "delete_any_quran::grade",
                    "force_delete_quran::grade",
                    "force_delete_any_quran::grade",
                    "view_role",
                    "view_any_role",
                    "create_role",
                    "update_role",
                    "delete_role",
                    "delete_any_role",
                    "view_student",
                    "view_any_student",
                    "create_student",
                    "update_student",
                    "restore_student",
                    "restore_any_student",
                    "replicate_student",
                    "reorder_student",
                    "delete_student",
                    "delete_any_student",
                    "force_delete_student",
                    "force_delete_any_student",
                    "view_student::extracurricular",
                    "view_any_student::extracurricular",
                    "create_student::extracurricular",
                    "update_student::extracurricular",
                    "restore_student::extracurricular",
                    "restore_any_student::extracurricular",
                    "replicate_student::extracurricular",
                    "reorder_student::extracurricular",
                    "delete_student::extracurricular",
                    "delete_any_student::extracurricular",
                    "force_delete_student::extracurricular",
                    "force_delete_any_student::extracurricular",
                    "view_student::inactive",
                    "view_any_student::inactive",
                    "create_student::inactive",
                    "update_student::inactive",
                    "restore_student::inactive",
                    "restore_any_student::inactive",
                    "replicate_student::inactive",
                    "reorder_student::inactive",
                    "delete_student::inactive",
                    "delete_any_student::inactive",
                    "force_delete_student::inactive",
                    "force_delete_any_student::inactive",
                    "view_subject",
                    "view_any_subject",
                    "create_subject",
                    "update_subject",
                    "restore_subject",
                    "restore_any_subject",
                    "replicate_subject",
                    "reorder_subject",
                    "delete_subject",
                    "delete_any_subject",
                    "force_delete_subject",
                    "force_delete_any_subject",
                    "view_teacher",
                    "view_any_teacher",
                    "create_teacher",
                    "update_teacher",
                    "restore_teacher",
                    "restore_any_teacher",
                    "replicate_teacher",
                    "reorder_teacher",
                    "delete_teacher",
                    "delete_any_teacher",
                    "force_delete_teacher",
                    "force_delete_any_teacher",
                    "view_user",
                    "view_any_user",
                    "create_user",
                    "update_user",
                    "restore_user",
                    "restore_any_user",
                    "replicate_user",
                    "reorder_user",
                    "delete_user",
                    "delete_any_user",
                    "force_delete_user",
                    "force_delete_any_user",
                    "page_Assessment",
                    "page_AssessmentQuran",
                    "page_Leger",
                    "page_LegerQuran",
                    "page_MyGrade",
                    "page_MySubject",
                    "page_SchoolSettingPage",
                    "page_ScoreCriteriaSettingPage",
                    "page_EditProfilePage",
                    "widget_AcademicYearWidget",
                    "widget_UserOnlineWidget",
                    "widget_TeacherWidget",
                    "widget_StudentWidget",
                    "widget_GradeWidget"
                ]
            },
            {
                "name": "student",
                "guard_name": "web",
                "permissions": []
            },
            {
                "name": "teacher",
                "guard_name": "web",
                "permissions": []
            },
            {
                "name": "teacher_subject",
                "guard_name": "web",
                "permissions": [
                    "page_MySubject",
                    "page_Assessment",
                    "page_Leger",
                    "view_competency",
                    "view_any_competency",
                    "create_competency",
                    "update_competency",
                    "restore_competency",
                    "restore_any_competency",
                    "replicate_competency",
                    "reorder_competency",
                    "delete_competency",
                    "delete_any_competency",
                    "force_delete_competency",
                    "force_delete_any_competency"
                ]
            },
            {
                "name": "teacher_grade",
                "guard_name": "web",
                "permissions": [
                    "page_MyGrade",
                    "view_attendance",
                    "view_any_attendance",
                    "create_attendance",
                    "update_attendance",
                    "restore_attendance",
                    "restore_any_attendance",
                    "replicate_attendance",
                    "reorder_attendance",
                    "delete_attendance",
                    "delete_any_attendance",
                    "force_delete_attendance",
                    "force_delete_any_attendance",
                    "view_attitude",
                    "view_any_attitude",
                    "create_attitude",
                    "update_attitude",
                    "restore_attitude",
                    "restore_any_attitude",
                    "replicate_attitude",
                    "reorder_attitude",
                    "delete_attitude",
                    "delete_any_attitude",
                    "force_delete_attitude",
                    "force_delete_any_attitude"
                ]
            },
            {
                "name": "teacher_quran",
                "guard_name": "web",
                "permissions": [
                "view_any_quran::grade",
                    "create_quran::grade",
                    "update_quran::grade",
                    "restore_quran::grade",
                    "restore_any_quran::grade",
                    "replicate_quran::grade",
                    "reorder_quran::grade",
                    "delete_quran::grade",
                    "delete_any_quran::grade",
                    "force_delete_quran::grade",
                    "force_delete_any_quran::grade",
                    "view_competency::quran",
                    "view_any_competency::quran",
                    "create_competency::quran",
                    "update_competency::quran",
                    "restore_competency::quran",
                    "restore_any_competency::quran",
                    "replicate_competency::quran",
                    "reorder_competency::quran",
                    "delete_competency::quran",
                    "delete_any_competency::quran",
                    "force_delete_competency::quran",
                    "force_delete_any_competency::quran",
                    "page_AssessmentQuran",
                    "page_LegerQuran"
                ]
            },
            {
                "name": "teacher_extracurricular",
                "guard_name": "web",
                "permissions": [
                    "view_student::extracurricular",
                    "view_any_student::extracurricular",
                    "create_student::extracurricular",
                    "update_student::extracurricular",
                    "restore_student::extracurricular",
                    "restore_any_student::extracurricular",
                    "replicate_student::extracurricular",
                    "reorder_student::extracurricular",
                    "delete_student::extracurricular",
                    "delete_any_student::extracurricular",
                    "force_delete_student::extracurricular",
                    "force_delete_any_student::extracurricular"
                ]
            },
            {
                "name": "project_coordinator",
                "guard_name": "web",
                "permissions": [
                    "view_project",
                    "view_any_project",
                    "create_project",
                    "update_project",
                    "restore_project",
                    "restore_any_project",
                    "replicate_project",
                    "reorder_project",
                    "delete_project",
                    "delete_any_project",
                    "force_delete_project",
                    "force_delete_any_project",
                    "page_ProjectAssesment",
                    "page_ProjectNote"
                ]
            },
            {
                "name": "admin",
                "guard_name": "web",
                "permissions": [
                    "view_academic::year",
                    "view_any_academic::year",
                    "create_academic::year",
                    "update_academic::year",
                    "restore_academic::year",
                    "restore_any_academic::year",
                    "replicate_academic::year",
                    "reorder_academic::year",
                    "delete_academic::year",
                    "delete_any_academic::year",
                    "force_delete_academic::year",
                    "force_delete_any_academic::year",
                    "view_extracurricular",
                    "view_any_extracurricular",
                    "create_extracurricular",
                    "update_extracurricular",
                    "restore_extracurricular",
                    "restore_any_extracurricular",
                    "replicate_extracurricular",
                    "reorder_extracurricular",
                    "delete_extracurricular",
                    "delete_any_extracurricular",
                    "force_delete_extracurricular",
                    "force_delete_any_extracurricular",
                    "view_student::extracurricular",
                    "view_any_student::extracurricular",
                    "create_student::extracurricular",
                    "update_student::extracurricular",
                    "restore_student::extracurricular",
                    "restore_any_student::extracurricular",
                    "replicate_student::extracurricular",
                    "reorder_student::extracurricular",
                    "delete_student::extracurricular",
                    "delete_any_student::extracurricular",
                    "force_delete_student::extracurricular",
                    "force_delete_any_student::extracurricular",
                    "view_grade",
                    "view_any_grade",
                    "create_grade",
                    "update_grade",
                    "restore_grade",
                    "restore_any_grade",
                    "replicate_grade",
                    "reorder_grade",
                    "delete_grade",
                    "delete_any_grade",
                    "force_delete_grade",
                    "force_delete_any_grade",
                    "view_project::coordinator",
                    "view_any_project::coordinator",
                    "create_project::coordinator",
                    "update_project::coordinator",
                    "restore_project::coordinator",
                    "restore_any_project::coordinator",
                    "replicate_project::coordinator",
                    "reorder_project::coordinator",
                    "delete_project::coordinator",
                    "delete_any_project::coordinator",
                    "force_delete_project::coordinator",
                    "force_delete_any_project::coordinator",
                    "view_student",
                    "view_any_student",
                    "create_student",
                    "update_student",
                    "restore_student",
                    "restore_any_student",
                    "replicate_student",
                    "reorder_student",
                    "delete_student",
                    "delete_any_student",
                    "force_delete_student",
                    "force_delete_any_student",
                    "view_subject",
                    "view_any_subject",
                    "create_subject",
                    "update_subject",
                    "restore_subject",
                    "restore_any_subject",
                    "replicate_subject",
                    "reorder_subject",
                    "delete_subject",
                    "delete_any_subject",
                    "force_delete_subject",
                    "force_delete_any_subject",
                    "view_teacher",
                    "view_any_teacher",
                    "create_teacher",
                    "update_teacher",
                    "restore_teacher",
                    "restore_any_teacher",
                    "replicate_teacher",
                    "reorder_teacher",
                    "delete_teacher",
                    "delete_any_teacher",
                    "force_delete_teacher",
                    "force_delete_any_teacher",
                    "view_user",
                    "view_any_user",
                    "create_user",
                    "update_user",
                    "restore_user",
                    "restore_any_user",
                    "replicate_user",
                    "reorder_user",
                    "delete_user",
                    "delete_any_user",
                    "force_delete_user",
                    "force_delete_any_user",
                    "view_teacher::quran",
                    "view_any_teacher::quran",
                    "create_teacher::quran",
                    "update_teacher::quran",
                    "restore_teacher::quran",
                    "restore_any_teacher::quran",
                    "replicate_teacher::quran",
                    "reorder_teacher::quran",
                    "delete_teacher::quran",
                    "delete_any_teacher::quran",
                    "force_delete_teacher::quran",
                    "force_delete_any_teacher::quran"
                ]
            }
        ]
        ';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
