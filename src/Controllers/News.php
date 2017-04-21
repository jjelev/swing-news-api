<?php

namespace Swing\Controllers;

use PDOException;
use Swing\Models\News as Model;
use Swing\Request;
use Swing\Response;
use Swing\ValidatorTrait;

class News
{
    use ValidatorTrait;

    /**
     * @var Model
     */
    protected $db;

    function __construct(Model $model)
    {
        $this->db = new $model;
    }

    public function get($id = null)
    {
        $this->validate($id, 'nullable|integer');

        if ($this->validationFails()) {
            $response = new Response([
                'errors' => $this->validationErrors()
            ]);

            return $response->json();
        }

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

        $this->validate($title, 'string|max:255');
        $this->validate($text, 'string|max:10000');

        if ($this->validationFails()) {
            $response = new Response([
                'errors' => $this->validationErrors()
            ]);

            return $response->json();
        }

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

        $this->validate($title, 'string|max:255');
        $this->validate($text, 'string|max:10000');
        $this->validate($date, 'string|max:10000');

        if ($this->validationFails()) {
            $response = new Response([
                'errors' => $this->validationErrors()
            ]);

            return $response->json();
        }

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
        $this->validate($id, 'integer');

        if ($this->validationFails()) {
            $response = new Response([
                'errors' => $this->validationErrors()
            ]);

            return $response->json();
        }

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