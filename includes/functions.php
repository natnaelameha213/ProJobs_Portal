<?php
/**
 * Shared helper functions
 */

function time_ago($datetime) {
    $ts = is_numeric($datetime) ? $datetime : strtotime($datetime);
    $diff = time() - $ts;
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    return date('M j, Y', $ts);
}

function job_image_url($path, $category = null) {
    if (!empty($path)) {
        // External URL (Unsplash etc.)
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        // Local uploaded file
        if (file_exists(__DIR__ . '/../' . $path)) {
            return base_url($path);
        }
    }
    // Category-specific professional Unsplash fallbacks (free, high quality)
    $fallbacks = [
        'information technology' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=600&h=320&fit=crop',
        'design & creative' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=600&h=320&fit=crop',
        'marketing & sales' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&h=320&fit=crop',
        'engineering' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=600&h=320&fit=crop',
        'healthcare' => 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=600&h=320&fit=crop',
        'education' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=600&h=320&fit=crop',
        'finance & accounting' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=600&h=320&fit=crop',
        'customer service' => 'https://images.unsplash.com/photo-1556745757-8d76bdb6984b?w=600&h=320&fit=crop',
        'logistics & supply chain' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=600&h=320&fit=crop',
        'human resources' => 'https://images.unsplash.com/photo-1521737711867-e3b97375f902?w=600&h=320&fit=crop',
    ];
    $key = strtolower(trim((string)$category));
    if ($key && isset($fallbacks[$key])) {
        return $fallbacks[$key];
    }
    // Default professional office/career image
    return 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=600&h=320&fit=crop';
}

function company_logo_url($path) {
    if (!empty($path) && file_exists(__DIR__ . '/../' . $path)) {
        return base_url($path);
    }
    return 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 80 80"><rect width="80" height="80" rx="12" fill="#FFF4EB"/><rect x="20" y="28" width="40" height="28" rx="4" fill="none" stroke="#FF6B00" stroke-width="3"/><path d="M28 28 V24 A12 12 0 0 1 52 24 V28" fill="none" stroke="#FF6B00" stroke-width="3"/></svg>');
}

function avatar_url($path) {
    if (!empty($path) && file_exists(__DIR__ . '/../' . $path)) {
        return base_url($path);
    }
    return 'data:image/svg+xml,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50" fill="#c7d2fe"/><circle cx="50" cy="38" r="18" fill="#6366f1"/><ellipse cx="50" cy="78" rx="28" ry="20" fill="#6366f1"/></svg>');
}

function upload_file($file, $subdir, $allowed_types, $max_size) {
    if (empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return [null, 'No file uploaded or upload error.'];
    }
    if ($file['size'] > $max_size) {
        return [null, 'File is too large.'];
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, $allowed_types, true)) {
        return [null, 'Invalid file type.'];
    }
    $ext = match ($mime) {
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        'application/pdf' => 'pdf',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        default => 'bin',
    };
    $dir = UPLOAD_DIR . $subdir . '/';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $name = bin2hex(random_bytes(12)) . '.' . $ext;
    $dest = $dir . $name;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return [null, 'Failed to save file.'];
    }
    return [UPLOAD_URL . $subdir . '/' . $name, null];
}

function get_job_applicant_count(PDO $pdo, $job_id) {
    $s = $pdo->prepare('SELECT COUNT(*) FROM applications WHERE job_id = ?');
    $s->execute([$job_id]);
    return (int)$s->fetchColumn();
}

function has_applied(PDO $pdo, $job_id, $email) {
    $s = $pdo->prepare('SELECT id FROM applications WHERE job_id = ? AND email = ?');
    $s->execute([$job_id, $email]);
    return (bool)$s->fetch();
}

function is_job_saved(PDO $pdo, $user_id, $job_id) {
    $s = $pdo->prepare('SELECT id FROM saved_jobs WHERE user_id = ? AND job_id = ?');
    $s->execute([$user_id, $job_id]);
    return (bool)$s->fetch();
}

function status_badge($status) {
    $map = [
        'Pending' => 'warning',
        'Reviewed' => 'info',
        'Shortlisted' => 'primary',
        'Rejected' => 'danger',
        'Hired' => 'success',
        'open' => 'success',
        'closed' => 'secondary',
        'Active' => 'success',
    ];
    $cls = $map[$status] ?? 'secondary';
    return '<span class="badge text-bg-' . $cls . '">' . e($status) . '</span>';
}

function nl2br_safe($text) {
    return nl2br(e($text));
}
