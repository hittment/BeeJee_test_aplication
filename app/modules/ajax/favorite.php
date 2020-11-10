<?php
error_reporting(0);
defined('_#FWLone') or die('Restricted access');

if (!isset($_SESSION['favorite'])) $_SESSION['favorite'] = [];

if (isset($_POST)) {
    if (in_array($_POST['id'], $_SESSION['favorite'])) {
        unset($_SESSION['favorite'][$_POST['id']]);
        if (count($_SESSION['favorite']) > 0) {
            echo 'deleted';
        } else {
            unset($_SESSION['favorite']);
            echo 'all_deleted';
        }
    } else {
        $_SESSION['favorite'][$_POST['id']] = $_POST['id'];
        echo 'saved';
    }
}

die;