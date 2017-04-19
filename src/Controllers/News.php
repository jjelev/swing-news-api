<?php

namespace Swing\Controllers;

use PDOException;
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

    public function get(?int $id = null)
    {
        //TODO: Validation!

        try {
            $content = $this->db->select($id);

        } catch (PDOException $e) {
            $content = ['errors' => [$e->getMessage()]];
        }

        $response = new Response($content);

        $response->json();
    }

    public function create(Request $request)
    {
        $title = $request->input('title');
        $text = $request->input('text');

        try {
            $result = $this->db->insert($title, $text);
            $content = (['result' => $result]);

        } catch (PDOException $e) {
            $content = ['errors' => [$e->getMessage()]];
        }

        $response = new Response();

        $response->setContent($content);

        $response->json();
    }

    public function update(Request $request, int $id)
    {
        $title = $request->input('title');
        $text = $request->input('text');
        $date = $request->input('date');

        try {
            $result = $this->db->update($id, $title, $text, $date);
            $content = (['result' => $result]);

        } catch (PDOException $e) {
            $content = ['errors' => [$e->getMessage()]];
        }

        $response = new Response();

        $response->setContent($content);

        $response->json();
    }

    public function delete(int $id)
    {
        try {
            $result = $this->db->delete($id);
            $content = (['result' => $result]);

        } catch (PDOException $e) {
            $content = ['errors' => [$e->getMessage()]];
        }

        $response = new Response();

        $response->setContent($content);

        $response->json();
    }
}