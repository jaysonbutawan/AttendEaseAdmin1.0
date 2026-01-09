<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    /**
     * Laravel normally expects an auto-incrementing integer "id".
     * But your table uses a string primary key: teacher_id.
     */
    protected $primaryKey = 'teacher_id';
    public $incrementing = false;       // because it's not auto-increment
    protected $keyType = 'string';      // teacher_id is a string

    protected $table = 'teachers';

    /**
     * Fillable columns (mass assignable)
     */
    protected $fillable = [
        'teacher_id',
        'firebase_uid',
        'email',
        'contact_number',
        'firstname',
        'lastname',
        'password',
        'status',
        'approval_status',
        'approved_at',
    ];

    /**
     * Disable timestamps because your table only has created_at
     */
    public $timestamps = false;

    /**
     * Get the class sessions (subjects) assigned to this teacher
     */
    public function classSessions()
    {
        return $this->hasMany(ClassSession::class, 'teacher_id', 'teacher_id');
    }

    /**
     * Get all unique subjects assigned to this teacher
     */
    public function assignedSubjects()
    {
        return $this->hasManyThrough(
            Subject::class,
            ClassSession::class,
            'teacher_id',
            'subject_id',
            'teacher_id',
            'subject_id'
        )->distinct();
    }

    /**
     * Get teacher profile data with assigned subjects
     * Returns: name, email, department (derived from subjects), assigned subjects
     */
    public function getProfileData()
    {
        $subjects = $this->classSessions()
            ->with('subject')
            ->get()
            ->pluck('subject')
            ->filter()
            ->unique('subject_id')
            ->values()
            ->map(function ($subject) {
                return [
                    'id' => $subject->subject_id,
                    'name' => $subject->subject_name,
                ];
            });

        // Derive department from subjects or mark as 'Not Assigned'
        $department = 'Not Assigned';
        if ($subjects->isNotEmpty()) {
            // Get the first subject name as department indicator
            $firstSubject = $subjects->first()['name'];
            // You can add logic here to map subjects to departments
            // For now, we'll use a simple mapping
            $departmentMap = [
                'Physics' => 'Science',
                'Chemistry' => 'Science',
                'Biology' => 'Science',
                'Advanced Science' => 'Science',
                'Environmental Science' => 'Science',
                'Algebra' => 'Mathematics',
                'Geometry' => 'Mathematics',
                'Calculus' => 'Mathematics',
                'Statistics' => 'Mathematics',
                'Trigonometry' => 'Mathematics',
                'Literature' => 'English',
                'Writing' => 'English',
                'Creative Writing' => 'English',
                'Programming' => 'Computer Science',
                'Data Structures' => 'Computer Science',
            ];
            
            foreach ($subjects as $subject) {
                if (isset($departmentMap[$subject['name']])) {
                    $department = $departmentMap[$subject['name']];
                    break;
                }
            }
        }

        return [
            'id' => $this->teacher_id,
            'name' => trim($this->firstname . ' ' . $this->lastname),
            'email' => $this->email,
            'department' => $department,
            'assignedSubjects' => $subjects->toArray(),
        ];
    }
}
