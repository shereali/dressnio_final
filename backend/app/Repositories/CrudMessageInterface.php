<?php

namespace App\Repositories;

interface CrudMessageInterface
{
    public function createMsg();
    public function updateMsg();
    public function deleteMsg();
}
