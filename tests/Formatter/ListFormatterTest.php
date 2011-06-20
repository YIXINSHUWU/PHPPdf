<?php

use PHPPdf\Glyph\BasicList;
use PHPPdf\Document;
use PHPPdf\Formatter\ListFormatter;

class ListFormatterTest extends TestCase
{
    private $formatter;
    
    public function setUp()
    {
        $this->formatter = new ListFormatter();
    }
    
    /**
     * @test
     */
    public function ifListsPositionIsOutsidePositionOfChildrenWontBeTranslated()
    {
        $list = $this->getMock('PHPPdf\Glyph\BasicList', array('getChildren', 'getAttribute'));
        
        $list->expects($this->once())
             ->method('getAttribute')
             ->with('position')
             ->will($this->returnValue(BasicList::POSITION_OUTSIDE));

        $list->expects($this->never())
             ->method('getChildren');
             
        $this->formatter->format($list, new Document());
    }
    
    /**
     * @test
     */
    public function ifListsPositionIsInsidePositionOfChildrenWillBeTranslated()
    {
        $widthOfEnumerationChar = 7;
        
        $list = $this->getMock('PHPPdf\Glyph\BasicList', array('getChildren', 'getWidthOfEnumerationChar', 'getAttribute'));
        
        $list->expects($this->once())
             ->method('getWidthOfEnumerationChar')
             ->will($this->returnValue($widthOfEnumerationChar));
        $list->expects($this->at(0))
             ->method('getAttribute')
             ->with('position')
             ->will($this->returnValue(BasicList::POSITION_INSIDE));

        $children = array();
        $leftMargin = 10;
        for($i=0; $i<2; $i++)
        {
            $child = $this->getMock('PHPPdf\Glyph\Container', array('setAttribute', 'getMarginLeft'));
            $child->expects($this->once())
                  ->method('getMarginLeft')
                  ->will($this->returnValue($leftMargin));
            $child->expects($this->once())
                  ->method('setAttribute')
                  ->with('margin-left', $widthOfEnumerationChar + $leftMargin);
            $children[] = $child;
        }
        
        $list->expects($this->atLeastOnce())
             ->method('getChildren')
             ->will($this->returnValue($children));
             
        $this->formatter->format($list, new Document());
    }
}