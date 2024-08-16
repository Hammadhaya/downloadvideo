<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VideoDownloader extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->library('session'); // Load the session library
    }

    public function index() {
        $this->load->view('video_downloader_form');
    }

    public function get_video_info() {
        $video_url = $this->input->post('video_url');
        if (empty($video_url)) {
            $this->session->set_flashdata('error', 'Video URL is required.');
            redirect('videodownloader');
        }

        // Execute yt-dlp command to get available formats
        $command = "yt-dlp -F " . escapeshellarg($video_url) . " 2>&1";
        $output = shell_exec($command);
        
        // Check if the output contains any formats
        if (strpos($output, 'format code') === false) {
            // No formats found, start download with default format
            $this->session->set_flashdata('success', 'No formats found.');
            $this->download_video_default($video_url); // Call the method to download with a default format
            return;
        }

        $data['video_info'] = $output;
        $data['video_url'] = $video_url;
        $this->load->view('video_info', $data);
    }

    public function download_video() {
        $video_url = $this->input->post('video_url');
        $format_id = $this->input->post('format_id');

        if (empty($video_url) || empty($format_id)) {
            $this->session->set_flashdata('error', 'Both video URL and format ID are required.');
            redirect('videodownloader');
        }

        $this->session->set_userdata('download_progress', 0);

        $temp_dir = 'C:/wamp64/www/examplevideo/temp';
        if (!is_dir($temp_dir)) {
            mkdir($temp_dir, 0777, true);
        }

        // Execute yt-dlp command to download the video with progress tracking
        $temp_file = tempnam($temp_dir, 'video_') . '.mp4';
        $command = "yt-dlp -f " . escapeshellarg($format_id) . " " . escapeshellarg($video_url) . " -o " . escapeshellarg($temp_file) . " --newline";
        $process = popen($command, 'r');

        while (!feof($process)) {
            $output = fgets($process);
            if (preg_match('/\b(\d{1,3})\%/', $output, $matches)) {
                $progress = intval($matches[1]);
                $this->session->set_userdata('download_progress', $progress);
            }
        }

        pclose($process);
        $this->session->set_userdata('download_progress', 100);

        if (file_exists($temp_file)) {
            $this->output_video($temp_file, basename($video_url) . '.mp4');
        } else {
            $this->session->set_flashdata('error', 'Failed to start download.');
            redirect('videodownloader');
        }
    }

    public function get_progress() {
        $progress = $this->session->userdata('download_progress');
        echo json_encode(['progress' => $progress]);
    }

    private function output_video($file_path, $download_name) {
        if (file_exists($file_path)) {
            $this->load->helper('file');
            $mime = get_mime_by_extension($file_path);
            header('Content-Description: File Transfer');
            header('Content-Type: ' . $mime);
            header('Content-Disposition: attachment; filename="' . $download_name . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            unlink($file_path); // Delete the temporary file
            exit;
        } else {
            $this->session->set_flashdata('error', 'Failed to download video.');
            redirect('videodownloader');
        }
    }

    public function download_video_default($video_url) {
        $default_format_id = 'best';
        
        $temp_dir = 'C:/wamp64/www/examplevideo/temp';
        if (!is_dir($temp_dir)) {
            mkdir($temp_dir, 0777, true);
        }

        $this->session->set_userdata('download_progress', 0);

        $temp_file = tempnam($temp_dir, 'video_') . '.mp4';
        $command = "yt-dlp -f " . escapeshellarg($default_format_id) . " " . escapeshellarg($video_url) . " -o " . escapeshellarg($temp_file) . " --newline";
        $process = popen($command, 'r');

        while (!feof($process)) {
            $output = fgets($process);
            if (preg_match('/\b(\d{1,3})\%/', $output, $matches)) {
                $progress = intval($matches[1]);
                $this->session->set_userdata('download_progress', $progress);
            }
        }

        pclose($process);
        $this->session->set_userdata('download_progress', 100);

        if (file_exists($temp_file)) {
            $this->output_video($temp_file, basename($video_url) . '.mp4');
        } else {
            $this->session->set_flashdata('error', 'Failed to start download.');
            redirect('videodownloader');
        }
    }
}









// defined('BASEPATH') OR exit('No direct script access allowed');

// class VideoDownloader extends CI_Controller {

//     public function __construct() {
//         parent::__construct();
//         $this->load->helper('url');
//         $this->load->helper('form');
//         $this->load->library('session');
//     }

//     public function index() {
//         $this->load->view('video_downloader_form');
//     }

//     public function get_video_info() {
//         $video_url = $this->input->post('video_url');
//         if (empty($video_url)) {
//             $this->session->set_flashdata('error', 'Video URL is required.');
//             redirect('videodownloader');
//         }

//         $command = "yt-dlp -F " . escapeshellarg($video_url) . " 2>&1";
//         $output = shell_exec($command);
        
//         if (strpos($output, 'format code') === false) {
//             $this->session->set_flashdata('success', 'No formats found. Starting download with default format.');
//             $this->download_video_default($video_url);
//             return;
//         }

//         $data['video_info'] = $output;
//         $data['video_url'] = $video_url;
//         $this->load->view('video_info', $data);
//     }

//     public function download_video() {
//         $video_url = $this->input->post('video_url');
//         $format_id = $this->input->post('format_id');

//         if (empty($video_url) || empty($format_id)) {
//             $this->session->set_flashdata('error', 'Both video URL and format ID are required.');
//             redirect('videodownloader');
//         }

//         $temp_dir = 'C:/wamp64/www/examplevideo/temp';
//         if (!is_dir($temp_dir)) {
//             mkdir($temp_dir, 0777, true);
//         }

//         $temp_file = tempnam($temp_dir, 'video_') . '.mp4';
//         $command = "yt-dlp -f " . escapeshellarg($format_id) . " " . escapeshellarg($video_url) . " -o " . escapeshellarg($temp_file) . " 2>&1";
//         $output = shell_exec($command);

//         if (file_exists($temp_file)) {
//             $download_link = base_url('temp/' . basename($temp_file));
//             $this->session->set_flashdata('success', 'Video ready for download: <a href="'.$download_link.'" target="_blank">Download Video</a>');
//         } else {
//             $this->session->set_flashdata('error', 'Failed to start download.');
//         }

//         redirect('videodownloader');
//     }

//     public function download_video_default($video_url) {
//         $default_format_id = 'best';
    
//         $temp_dir = 'C:/wamp64/www/examplevideo/temp';
//         if (!is_dir($temp_dir)) {
//             mkdir($temp_dir, 0777, true);
//         }
    
//         $temp_file = tempnam($temp_dir, 'video_') . '.mp4';
//         $command = "yt-dlp -f " . escapeshellarg($default_format_id) . " " . escapeshellarg($video_url) . " -o " . escapeshellarg($temp_file) . " 2>&1";
//         $output = shell_exec($command);
    
//         if (file_exists($temp_file)) {
//             $download_link = base_url('temp/' . basename($temp_file));
//             $this->session->set_flashdata('success', 'Video ready for download: <a href="'.$download_link.'" target="_blank">Download Video</a>');
//         } else {
//             $this->session->set_flashdata('error', 'Failed to start download with default format.');
//         }
        
//         redirect('videodownloader');
//     }
// }
