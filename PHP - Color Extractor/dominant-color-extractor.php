<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <title>Dominant Color Extractor</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="header">
        <h1>Dominant Colors Extractor</h1>
        <p>Extract the dominant colors used in an image</p>
    </div>
    <?php
    if (isset($_POST['submit'])) {
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            // Check file size
            if ($_FILES["image"]["size"] > 500000) {
                echo "<script>alert('Sorry, your file is too large.')</script>";
            } else {
                // Check file extension
                $extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
                if ($extension != "jpg" && $extension != "png" && $extension != "jpeg") {
                    echo "<script>alert('Sorry, only JPG, JPEG, files are allowed.')</script>";
                } else {
                    // Delete previous image
                    if (file_exists("image.jpg")) {
                        unlink("image.jpg");
                    } else if (file_exists("image.png")) {
                        unlink("image.png");
                    }
                    // Upload file
                    if ($extension == "jpg" || $extension == "jpeg") {
                        move_uploaded_file($_FILES["image"]["tmp_name"], "image.jpg");
                    } else if ($extension == "png") {
                        move_uploaded_file($_FILES["image"]["tmp_name"], "image.png");
                    }
                }
            }
        } else {
            echo "<script>alert('File is not an image.')</script>";
        }
        echo "<script>window.open('dominant-color-extractor.php','_self')</script>";
    }

    // Get the image 
    if (file_exists("image.jpg")) {
        $image = imagecreatefromjpeg("image.jpg");
    } else if (file_exists("image.png")) {
        $image = imagecreatefrompng("image.png");
    } else {
        $image = imagecreatefromjpeg("default.jpg");
    }

    // Get the Image size
    $width = imagesx($image);
    $height = imagesy($image);

    // Get the most used color
    $colors = [];
    for ($x = 0; $x < $width; $x++) {
        for ($y = 0; $y < $height; $y++) {

            // Get the RGB Value of current pixel
            $rgb = imagecolorat($image, $x, $y);

            // Extract each value for r, g, b
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            // Get the value from the rgb value
            $hex = dechex($r) . dechex($g) . dechex($b);

            // Increase the count if the color exists
            if (array_key_exists($hex, $colors)) {
                $colors[$hex]++;
            } else {
                $colors[$hex] = 1;
            }
        }
    }

    // get their percentage
    $total = array_sum($colors);
    foreach ($colors as $hex => $count) {
        $colors[$hex] = round($count / $total * 100, 2);
    }

    // sort them by percentage
    arsort($colors);

    // Get 5 most used colors
    $colors = array_slice($colors, 0, 5);

    ?>
    <div class="container">
        <div class="card">
            <?php if (file_exists("image.jpg")) : ?>
                <img src="image.jpg" alt="image">
            <?php elseif (file_exists("image.png")) : ?>
                <img src="image.png" alt="image">
            <?php else : ?>
                <img src="default.jpg" alt="image">
            <?php endif; ?>
        </div>
        <div class="info">
            <div class="pallette">
                <?php foreach ($colors as $hex => $count) : ?>
                    <div class="list" style="background: radial-gradient(circle at 100px 100px, #<?php echo $hex; ?>, #000);">
                        <span class="hex">#<?php echo $hex; ?></span>
                        <span class="percent"><?php echo $count; ?>%</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="form">
            <form action="dominant-color-extractor.php" method="post" enctype="multipart/form-data">
                <input type="file" name="image" id="image">
                <button type="submit" name="submit">Upload</button>
            </form>
        </div>
    </div>
</body>
</html>

    <!-- 
        no code was directly copied from the internet
        the following sites were used as reference and research

        https://stackoverflow.com/questions/8730661/how-to-find-the-dominant-color-in-image
        https://www.the-art-of-web.com/php/extract-image-color/
        https://itecnote.com/tecnote/php-detect-main-colors-in-an-image-with-php/
        https://bookofzeus.com/articles/php/get-the-color-palette-for-an-image-using-php/
     -->