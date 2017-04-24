<?php

namespace Swing\Models;

use PHPUnit\Framework\TestCase;

class NewsTest extends TestCase
{
    /**
     * @var News
     */
    private $model;

    public function setUp()
    {
        (new \Dotenv\Dotenv(__DIR__ . '/../../'))->load();

        $this->model = new News();

        //TODO: 'TRUNCATE TABLE `swing-news`.news;' or test will fail second time (id=1 does not exist)
    }

    public function testInsert()
    {
        $title = 'First Title';
        $text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi tincidunt semper ligula, ut aliquet risus placerat sit amet. Pellentesque tincidunt condimentum laoreet. Etiam eleifend mauris nec iaculis dictum.';

        $result = $this->model->insert($title, $text);

        $this->assertTrue($result);
    }

    /**
     * @depends testInsert
     */
    public function testSelectSingle()
    {
        $result = $this->model->select(1);

        $this->assertCount(1, $result);

        $this->assertArrayHasKey(0, $result);

        $this->assertArrayHasKey('id', $result[0]);
        $this->assertArrayHasKey('user_id', $result[0]);
        $this->assertArrayHasKey('title', $result[0]);
        $this->assertArrayHasKey('text', $result[0]);
        $this->assertArrayHasKey('date', $result[0]);

        $this->assertEquals('1', $result[0]['id']);
        //and so on ...
    }

    /**
     * @depends testInsert
     */
    public function testSelectAll()
    {
        $result = $this->model->select();

        $this->assertArrayHasKey(0, $result);

        $this->assertGreaterThanOrEqual(1, count($result));
    }

    /**
     * @depends testInsert
     */
    public function testUpdateWithoutDate()
    {
        $title = 'Changed Title';
        $text = 'Maecenas finibus ipsum vel porttitor semper. Vestibulum tincidunt ipsum eu nisi ornare consectetur. Sed iaculis purus sed augue vulputate consequat.';

        $result = $this->model->update(1, $title, $text, null);

        $this->assertTrue($result);
    }

    /**
     * @depends testInsert
     */
    public function testUpdate()
    {
        $title = 'Changed ' . time();
        $text = 'Pellentesque non risus vel ligula lobortis finibus non tincidunt elit. Curabitur id est leo.';

        $result = $this->model->update(1, $title, $text, '2017-04-23 15:34:11');
        $this->assertTrue($result);
    }

    /**
     * @depends testInsert
     */
    public function testDelete()
    {
        $result = $this->model->delete(1);

        $this->assertTrue($result);
    }
}
