<?php
$images = [
    '/assets/images/hero-1280.webp' => ['/assets/images/hero-768.webp' => 768, '/assets/images/hero-1280.webp' => 1280, '/assets/images/hero-1920.webp' => 1920],
    '/assets/images/hero-room-1280.webp' => ['/assets/images/hero-room-768.webp' => 768, '/assets/images/hero-room-1280.webp' => 1280, '/assets/images/hero-room-1698.webp' => 1698],
    '/assets/images/hero-detail-1280.webp' => ['/assets/images/hero-detail-768.webp' => 768, '/assets/images/hero-detail-1280.webp' => 1280, '/assets/images/hero-detail-1699.webp' => 1699],
];
foreach (['team', 'ordinacija', 'cekaona'] as $name) $images['/assets/images/'.$name.'.webp'] = ['/assets/images/'.$name.'-640.webp' => 640, '/assets/images/'.$name.'.webp' => 1280];
foreach (['marta','natasa','petra','gabrijela'] as $name) $images[$name.'.webp'] = ['/assets/images/'.$name.'.webp' => 480, '/assets/images/'.$name.'-960.webp' => 960];
foreach (['01','02','03','04'] as $number) $images['/assets/images/zubi'.$number.'-1058.webp'] = ['/assets/images/zubi'.$number.'-640.webp' => 640, '/assets/images/zubi'.$number.'-1058.webp' => 1058];
foreach (['2393', '2491', '2694', '3003', '6053', '7457'] as $number) $images['/assets/images/rezultati-'.$number.'-1920.webp'] = ['/assets/images/rezultati-'.$number.'-480.webp' => 480, '/assets/images/rezultati-'.$number.'-960.webp' => 960, '/assets/images/rezultati-'.$number.'-1920.webp' => 1920];
return $images;
