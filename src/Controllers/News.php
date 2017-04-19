<?php

namespace Swing\Controllers;

use Swing\Models\News as Model;
use Swing\Request;
use Swing\Response;

class News
{
    /**
     * @var Model
     */
    protected $db;

    function __construct(Model $model)
    {
        $this->db = new $model;
    }

    public function get(Request $request, ?int $id = null)
    {
        //TODO: Validation!

        try {
            $result = $this->db->select($id);

        } catch (\PDOException $e) {
            return $e->getMessage();
        }

        $response = new Response($result);

        $response->json();
    }

    public function create()
    {

    }

    public function update(int $id)
    {

    }

    public function delete()
    {

    }
}