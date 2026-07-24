<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DirectorPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::findOrCreate('director', 'web');

        $permissions = [
            'academic_year.view', 'academic_year.create', 'academic_year.update', 'academic_year.delete',
            'academic_term.view', 'academic_term.create', 'academic_term.update', 'academic_term.delete',
            'academic_class.view', 'academic_class.create', 'academic_class.update', 'academic_class.delete',
            'student.view', 'student.create', 'student.update', 'student.delete',
            'student_enrollment.view', 'student_enrollment.create', 'student_enrollment.update', 'student_enrollment.delete',
            'guardian.view', 'guardian.create', 'guardian.update', 'guardian.delete',
            'teacher.view', 'teacher.create', 'teacher.update', 'teacher.delete',
            'user.view', 'user.create', 'user.update', 'user.delete',
            'subject.view', 'subject.create', 'subject.update', 'subject.delete',
            'subject_coefficient.view', 'subject_coefficient.create', 'subject_coefficient.update', 'subject_coefficient.delete',
            'program.view', 'program.create', 'program.update', 'program.delete',
            'grade_level.view', 'grade_level.create', 'grade_level.update', 'grade_level.delete',
            'classroom.view', 'classroom.create', 'classroom.update', 'classroom.delete',
            'attendance.view', 'attendance.create', 'attendance.update', 'attendance.delete',
            'examination.view', 'examination.create', 'examination.update', 'examination.delete',
            'gradebook.view', 'gradebook.create', 'gradebook.update', 'gradebook.delete',
            'grade.view', 'grade.create', 'grade.update', 'grade.delete',
            'finance.view', 'finance.create', 'finance.update', 'finance.delete',
            'communication.view', 'communication.create', 'communication.update', 'communication.delete',
            'report.view', 'report.create', 'report.update', 'report.delete',
            'dashboard.view',
            'setting.view',
        ];

        foreach ($permissions as $permission) {
            $role->givePermissionTo($permission);
        }
    }
}
