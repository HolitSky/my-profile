<?php
/**
 * Main API Endpoint
 * Returns all portfolio data in JSON format
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Cache-Control: no-cache, must-revalidate, max-age=0');

require_once __DIR__ . '/../config/config.php';

try {
    $db = getDB();
    
    // Get all data
    $data = [
        'about' => $db->query("SELECT * FROM about LIMIT 1")->fetch(),
        'contact' => $db->query("SELECT * FROM contact_info LIMIT 1")->fetch(),
        'skills' => $db->query("SELECT * FROM skills ORDER BY sort_order ASC, id DESC")->fetchAll(),
        'experience' => $db->query("SELECT * FROM experience ORDER BY start_date DESC")->fetchAll(),
        'education' => $db->query("SELECT * FROM education ORDER BY start_date DESC")->fetchAll(),
        'portfolio' => $db->query("SELECT * FROM portfolio ORDER BY sort_order ASC, id ASC")->fetchAll(),
        'services' => $db->query("SELECT * FROM services ORDER BY sort_order ASC, id DESC")->fetchAll(),
        'testimonials' => $db->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order ASC, id DESC")->fetchAll(),
    ];
    
    // Format dates
    foreach ($data['experience'] as &$exp) {
        $exp['period'] = date('M Y', strtotime($exp['start_date'])) . ' - ' . 
                        ($exp['is_current'] ? 'Present' : date('M Y', strtotime($exp['end_date'])));
    }
    
    foreach ($data['education'] as &$edu) {
        $edu['period'] = date('Y', strtotime($edu['start_date'])) . ' - ' . 
                        ($edu['is_current'] ? 'Present' : date('Y', strtotime($edu['end_date'])));
    }
    
    echo json_encode([
        'success' => true,
        'data' => $data,
        'timestamp' => time()
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error',
        'message' => $e->getMessage()
    ]);
}
