// ===================================================
// CLASS SESSION API - USAGE EXAMPLES
// ===================================================

/**
 * DATABASE STRUCTURE:
 * 
 * Table: class_sessions
 * +----------------+----------------------------------+------+-----+---------+----------------+
 * | Field          | Type                             | Null | Key | Default | Extra          |
 * +----------------+----------------------------------+------+-----+---------+----------------+
 * | session_id     | int unsigned                     | NO   | PRI | NULL    | auto_increment |
 * | subject_id     | int unsigned                     | NO   | MUL | NULL    |                |
 * | room_id        | int unsigned                     | NO   | MUL | NULL    |                |
 * | teacher_id     | varchar(50)                      | NO   | MUL | NULL    |                |
 * | start_time     | time                             | YES  |     | NULL    |                |
 * | end_time       | time                             | YES  |     | NULL    |                |
 * | session_date   | date                             | NO   |     | NULL    |                |
 * | session_status | enum('active','ended','pending') | NO   |     | pending |                |
 * | qr_code        | varchar(255)                     | YES  |     | NULL    |                |
 * | qr_valid       | tinyint(1)                       | NO   |     | 0       |                |
 * | allowance_time | int                              | YES  |     | NULL    |                |
 * | updated_at     | timestamp                        | YES  |     | NULL    |                |
 * | created_at     | timestamp                        | YES  |     | NULL    |                |
 * +----------------+----------------------------------+------+-----+---------+----------------+
 */

// ===================================================
// BACKEND (Laravel PHP)
// ===================================================

/**
 * The Laravel controller is located at:
 * app/Http/Controllers/ClassSessionController.php
 * 
 * API Routes (defined in routes/api.php):
 * - POST   /api/class-sessions         - Create new session
 * - GET    /api/class-sessions         - Get all sessions
 * - GET    /api/class-sessions/{id}    - Get single session
 * - PUT    /api/class-sessions/{id}    - Update session
 * - DELETE /api/class-sessions/{id}    - Delete session
 */

// ===================================================
// FRONTEND (Vue/TypeScript)
// ===================================================

// Example 1: Create a new class session
async function exampleCreateSession() {
    const sessionData = {
        subject_id: 1,
        teacher_id: 'TCH001',
        room_id: 101,
        start_time: '09:00:00',
        end_time: '10:30:00',
        session_date: '2025-12-27',
        session_status: 'pending',  // optional: 'active', 'ended', or 'pending'
        qr_code: null,              // optional
        qr_valid: false,            // optional
        allowance_time: 15          // optional: minutes
    };

    try {
        const csrf = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '';
        const response = await fetch('/api/class-sessions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify(sessionData),
            credentials: 'same-origin',
        });

        if (!response.ok) {
            const error = await response.json();
            throw new Error(error?.message ?? 'Failed to create session');
        }

        const data = await response.json();
        console.log('Session created:', data.session);
        return data;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
}

// Example 2: Get all class sessions
async function exampleGetAllSessions() {
    try {
        const response = await fetch('/api/class-sessions', {
            method: 'GET',
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin',
        });

        const data = await response.json();
        console.log('All sessions:', data.sessions);
        return data.sessions;
    } catch (error) {
        console.error('Error:', error);
    }
}

// Example 3: Get single session by ID
async function exampleGetSession(sessionId: number) {
    try {
        const response = await fetch(`/api/class-sessions/${sessionId}`, {
            method: 'GET',
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin',
        });

        const data = await response.json();
        console.log('Session:', data.session);
        return data.session;
    } catch (error) {
        console.error('Error:', error);
    }
}

// Example 4: Update a class session
async function exampleUpdateSession(sessionId: number) {
    const updateData = {
        session_status: 'active',
        qr_code: 'QR123456789',
        qr_valid: true,
    };

    try {
        const csrf = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '';
        const response = await fetch(`/api/class-sessions/${sessionId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify(updateData),
            credentials: 'same-origin',
        });

        const data = await response.json();
        console.log('Session updated:', data.session);
        return data;
    } catch (error) {
        console.error('Error:', error);
    }
}

// Example 5: Delete a class session
async function exampleDeleteSession(sessionId: number) {
    try {
        const csrf = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '';
        const response = await fetch(`/api/class-sessions/${sessionId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            credentials: 'same-origin',
        });

        const data = await response.json();
        console.log('Session deleted:', data.message);
        return data;
    } catch (error) {
        console.error('Error:', error);
    }
}

// ===================================================
// RAW PHP/MySQL EXAMPLE (Alternative)
// ===================================================

/**
 * If you want to insert directly using PHP without Laravel:
 * 
 * <?php
 * $conn = new mysqli($servername, $username, $password, $dbname);
 * 
 * $stmt = $conn->prepare("
 *     INSERT INTO class_sessions 
 *     (subject_id, room_id, teacher_id, start_time, end_time, session_date, 
 *      session_status, qr_code, qr_valid, allowance_time) 
 *     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
 * ");
 * 
 * $stmt->bind_param("iissssssii", 
 *     $subject_id, $room_id, $teacher_id, $start_time, $end_time, 
 *     $session_date, $session_status, $qr_code, $qr_valid, $allowance_time
 * );
 * 
 * $subject_id = 1;
 * $room_id = 101;
 * $teacher_id = 'TCH001';
 * $start_time = '09:00:00';
 * $end_time = '10:30:00';
 * $session_date = '2025-12-27';
 * $session_status = 'pending';
 * $qr_code = null;
 * $qr_valid = 0;
 * $allowance_time = 15;
 * 
 * $stmt->execute();
 * echo "New session created with ID: " . $stmt->insert_id;
 * 
 * $stmt->close();
 * $conn->close();
 * ?>
 */

// ===================================================
// CURL EXAMPLE (Command Line/Terminal)
// ===================================================

/**
 * # Create a new class session using curl:
 * 
 * curl -X POST http://localhost:8000/api/class-sessions \
 *   -H "Content-Type: application/json" \
 *   -H "Accept: application/json" \
 *   -d '{
 *     "subject_id": 1,
 *     "teacher_id": "TCH001",
 *     "room_id": 101,
 *     "start_time": "09:00:00",
 *     "end_time": "10:30:00",
 *     "session_date": "2025-12-27",
 *     "session_status": "pending",
 *     "allowance_time": 15
 *   }'
 * 
 * # Get all sessions:
 * curl -X GET http://localhost:8000/api/class-sessions \
 *   -H "Accept: application/json"
 * 
 * # Get single session:
 * curl -X GET http://localhost:8000/api/class-sessions/1 \
 *   -H "Accept: application/json"
 */

export {
    exampleCreateSession,
    exampleGetAllSessions,
    exampleGetSession,
    exampleUpdateSession,
    exampleDeleteSession
};
