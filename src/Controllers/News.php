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
    protected $model;

    function __construct()
    {
        $this->model = new Model();
    }

    public function get($id = null)
    {
        $this->validate($id, ['nullable|integer']);

        if ($this->validationFails()) {
            $response = new Response([
                'errors' => $this->validationErrors()
            ]);

            return $response->json();
        }

        try {
            $content = $this->model->select($id);

        } catch (PDOException $e) {
            $content = ['errors' => [$e->getMessage()]];
        }

        $response = new Response($content);

        $response->json();
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'title' => 'string|min:4|max:255',
            'text' => 'string|min:4|max:10000',
        ]);

        if ($this->validationFails()) {
            $response = new Response([
                'errors' => $this->validationErrors()
            ]);

            return $response->json();
        }

        $title = $request->input('title');
        $text = $request->input('text');

        try {
            $result = $this->model->insert($title, $text);
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
        $this->validate($request, [
            'title' => 'string|min:4|max:255',
            'text' => 'string|min:4|max:10000',
            'date' => 'nullable|timestamp',
        ]);
        $this->validate($id, [
            'id' => 'nullable|integer'
        ]);

        if ($this->validationFails()) {
            $response = new Response([
                'errors' => $this->validationErrors()
            ]);

            return $response->json();
        }

        $title = $request->input('title');
        $text = $request->input('text');
        $date = $request->input('date');

        try {
            $result = $this->model->update($id, $title, $text, $date);
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
        $this->validate($id, ['integer']);

        if ($this->validationFails()) {
            $response = new Response([
                'errors' => $this->validationErrors()
            ]);

            return $response->json();
        }

        try {
            $result = $this->model->delete($id);
            $content = (['result' => $result]);

        } catch (PDOException $e) {
            $content = ['errors' => [$e->getMessage()]];
        }

        $response = new Response();

        $response->setContent($content);

        $response->json();
    }
}