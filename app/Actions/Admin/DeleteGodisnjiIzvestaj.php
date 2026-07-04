<?php

namespace App\Actions\Admin;

use App\Models\GodisnjIzvestaj;

class DeleteGodisnjiIzvestaj
{
    public function delete(GodisnjIzvestaj $izvestaj): void
    {
        $izvestaj->delete();
    }
}
