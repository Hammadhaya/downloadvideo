<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Downloader</title>
    <link rel="stylesheet" href="<?= base_url() ?>asset/style.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <style>
        .progress-bar-danger {
            background-color: #dc3545; /* Red */
        }
        .progress-bar-warning {
            background-color: #ffc107; /* Yellow */
        }
        .progress-bar-success {
            background-color: #28a745; /* Green */
        }
    </style>
</head>
<body>
    <header style="height: 50px;display:flex;">
        <div style="display: flex;align-items: center;">
            <img src="<?=base_url()?>asset/logo" alt="" style="height:175;width:173px">
        </div>
        <div style="display: flex;margin-left: 800px;">
            <nav>
                <a href="https://www.tiktok.com" target="_blank">TikTok</a>
                <a href="https://www.instagram.com" target="_blank">Instagram</a>
                <a href="https://www.snapchat.com" target="_blank">Snapchat</a>
                <a href="https://www.twitter.com" target="_blank">Twitter</a>
                <a href="https://www.youtube.com" target="_blank">YouTube</a>
            </nav>
        </div>
    </header>

    <section class="banner">
        <h1>Download Your Favorite Media</h1>
        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger">
                <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success">
                <?php echo $this->session->flashdata('success'); ?>
            </div>
        <?php endif; ?>
        <form action="<?php echo site_url('videodownloader/get_video_info'); ?>" method="post">
            <input type="text" id="video_url" name="video_url" class="form-control" placeholder="Enter video URL" required>
            <button type="submit" class="btn btn-primary">Click To Download</button>
        </form>
    </section>

    <div id="progress-container" style="display: none;">
        <div class="progress mt-3">
            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
        </div>
        <div id="progress-text" class="mt-2"></div>
    </div>

    <section class="videos" id="videos">
        <h2>Downloaded Videos</h2>
        <div class="video-item">
            <video width="320" height="240" controls>
                <source src="movie.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <p>My Movie</p>
        </div>
    </section>

    <script>
        $(document).ready(function() {
            function updateProgressBar(progress) {
                let progressBar = $('#progress-bar');
                progressBar.css('width', progress + '%');
                let progressText = $('#progress-text');
                progressText.text(progress + '%');
                
                if (progress > 3) {
                    progressBar.removeClass('progress-bar-warning progress-bar-success').addClass('progress-bar-danger');
                } else if (progress > 50) {
                    progressBar.removeClass('progress-bar-danger progress-bar-success').addClass('progress-bar-warning');
                } else {
                    progressBar.removeClass('progress-bar-danger progress-bar-warning').addClass('progress-bar-success');
                }
            }

            function checkProgress() {
                $.ajax({
                    url: '<?php echo site_url('videodownloader/get_progress'); ?>',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (data.progress < 100) {
                            updateProgressBar(data.progress);
                            setTimeout(checkProgress, 1000);
                        }
                         else {
                            updateProgressBar(100);
                            setTimeout(function() {
                                $('#progress-container').hide();
                            }, 2000);
                        }
                    }
                });
            }

            $('form').submit(function(e) {
                $('#progress-container').show();
                updateProgressBar(0);
                setTimeout(checkProgress, 1000);
            });
        });
    </script>
</body>
</html>
