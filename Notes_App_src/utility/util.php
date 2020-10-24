<?php

function allStringEntiresExist($array) {
    foreach($array as $value) {
        if (strlen(trim($value)) < 1) {
            return false;
        }
    }
    return true;
}
