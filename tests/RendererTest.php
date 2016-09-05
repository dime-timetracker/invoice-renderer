<?php
use PHPUnit\Framework\TestCase;
use Dime\InvoiceRenderer\Renderer;

class RendererTest extends TestCase
{
    protected function getMinimalArguments()
    {
        return [
            'sender' => "Homer Simpson\nEvergreen Terrace 42\nSpringfield\n\nUnited States",
            'receiver' => "Mary Poppins\n10 Grena Gardens\nRichmond\nTW9 1XN\nUnited Kingdom",
            'columns' => ['task' => 'task', 'duration' => 'duration'],
            'rows' => [['task' => 'Babysitting', 'duration' => '3 hours']],
            'totals' => ['grand total' => '£ 100.00']
        ];
    }

    protected function renderDefaultTemplate($args)
    {
        $renderer = new Renderer();
        return $renderer->setTemplate(dirname(__DIR__) . '/templates/default.twig')->html($args);
    }

    public function testWithMinimalArguments()
    {
        $args = $this->getMinimalArguments();
        $html = $this->renderDefaultTemplate($args);
        $this->assertTrue(
            strpos($html, '<address class="sender"><p>Homer Simpson</p><p>Evergreen T') > 0,
            'expected sender address was rendered'
        );
    }

    public function testWithASingleRowAndTwoColumns()
    {
        $args = $this->getMinimalArguments();
        $args['columns'] = ['desc' => 'Lýsing', 'duration' => 'Lengd'];
        $args['rows'] = [
            [ 'desc' => 'hugbúnaarþróun', 'duration' => '6:30 klst' ]
        ];
        $args['totals'] = [
            'subtotal' => ['title' => 'Samtals', 'value' => '630.00 kr.'],
            'tax' => ['title' => 'VSK 24%', 'value' => '151.20 kr.'],
            'grand_total' => ['title' => 'Grand alls', 'value' => '781.20 kr.']
        ];
        $html = $this->renderDefaultTemplate($args);
        $this->assertEquals(1, substr_count($html, '<tr class="row"'));
        $this->assertTrue(
            strpos($html, '<th class="desc">Lýsing</th>') > 0,
            'expected first column header was rendered'
        );
        $this->assertTrue(
            strpos($html, '<th class="duration">Lengd</th>') > 0,
            'expected second column header was rendered'
        );
        $this->assertTrue(
            strpos($html, '<td class="desc" title="Lýsing">hugbúnaarþróun</td>') > 0,
            'expected first row column was rendered'
        );
        $this->assertTrue(
            strpos($html, '<td class="duration" title="Lengd">6:30 klst</td>') > 0,
            'expected second row column was rendered'
        );
        $this->assertTrue(
            strpos($html, '<td class="subtotal label">Samtals</td>') > 0,
            'expected first total row column was rendered'
        );
        $this->assertTrue(
            strpos($html, '<td class="subtotal value">630.00 kr.</td>') > 0,
            'expected second total row column was rendered'
        );
    }

    public function testSurroundingText()
    {
        $args = $this->getMinimalArguments();
        $args['intro'] = "Dear Mary Poppins,\n\nYou are just great.";
        $args['outro'] = "you are just great.\n\nSincerly,\n\nMichael Banks";
        $html = $this->renderDefaultTemplate($args);
        $this->assertEquals(1, substr_count($html, '<tr class="row"'));
        $this->assertTrue(
            strpos($html, '<p class="intro text">Dear Mary Poppins,<br><br>You are just great.</p>') > 0,
            'expected intro was rendered'
        );
        $this->assertTrue(
            strpos($html, '<p class="outro text">you are just great.<br><br>Sincerly,<br><br>Michael Banks</p>') > 0,
            'expected outro was rendered'
        );
    }
}
