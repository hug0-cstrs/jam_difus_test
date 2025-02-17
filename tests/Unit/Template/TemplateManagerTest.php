<?php

require_once __DIR__ . '/../../TestCase.php';
require_once __DIR__ . '/../../../includes/template_manager.php';

class TemplateManagerTest extends TestCase
{
    private $templateManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->templateManager = TemplateManager::getInstance();
    }

    public function testRenderTemplate()
    {
        $data = [
            'title' => 'Test Title',
            'content' => 'Test Content'
        ];

        $template = '<div class="title">{{title}}</div><div class="content">{{content}}</div>';
        $expected = '<div class="title">Test Title</div><div class="content">Test Content</div>';

        $result = $this->templateManager->render($template, $data);
        $this->assertEquals($expected, $result);
    }

    public function testRenderTemplateWithNestedData()
    {
        $data = [
            'user' => [
                'name' => 'John Doe',
                'email' => 'john@example.com'
            ]
        ];

        $template = '<div>{{user.name}} ({{user.email}})</div>';
        $expected = '<div>John Doe (john@example.com)</div>';

        $result = $this->templateManager->render($template, $data);
        $this->assertEquals($expected, $result);
    }

    public function testRenderTemplateWithMissingData()
    {
        $data = [
            'title' => 'Test Title'
        ];

        $template = '<div>{{title}} {{content}}</div>';
        $expected = '<div>Test Title </div>';

        $result = $this->templateManager->render($template, $data);
        $this->assertEquals($expected, $result);
    }

    public function testLoadTemplateFile()
    {
        $templatePath = 'components/search_filters.html';
        $result = $this->templateManager->loadTemplate($templatePath);
        
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('form', strtolower($result->html()));
    }

    protected function tearDown(): void
    {
        $this->templateManager = null;
        parent::tearDown();
    }
} 