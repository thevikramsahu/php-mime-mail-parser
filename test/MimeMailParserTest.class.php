<?php
/*
* Class Test : MimeMailParserTest
*
* Liste des mails :
* m0001 : mail avec un fichier attaché de 1 ko
* m0002 : mail avec un fichier attaché de 3 ko
* m0003 : mail avec un fichier attaché de 14 ko
* m0004 : mail avec un fichier attaché de 800 ko
* m0005 : mail avec un fichier attaché de 1 500 ko
* m0006 : mail avec un fichier attaché de 3 196 ko
* m0007 : mail avec un fichier attaché sans content-disposition
*/
include_once(__DIR__."/../lib/MimeMailParser.class.php");

class MimeMailParserTest extends PHPUnit_Framework_TestCase {
	
	/**
	* @dataProvider provideMails
	*/
	function testGetAttachmentsWithText($mid, $nbAttachments, $size, $subject){
				
		$file = __DIR__."/mails/".$mid;
		$fd = fopen($file, "r");
		$contents = fread($fd, filesize($file));
		fclose($fd);

		$Parser = new MimeMailParser();
		$Parser->setText($contents);

		$this->assertEquals($subject,$Parser->getHeader('subject'));

		$attachments = $Parser->getAttachments();

		$this->assertEquals($nbAttachments,count($attachments));
		
		$save_dir = __DIR__."/mails/";
		foreach($attachments as $attachment) {
		  // get the attachment name
		  $filename = $attachment->filename;
		  // write the file to the directory you want to save it in
		  if ($fp = fopen($save_dir.$mid.'_'.$filename, 'w')) {
		    while($bytes = $attachment->read()) {
		      fwrite($fp, $bytes);
		    }
		    fclose($fp);
		  }
		  $this->assertEquals($size,filesize($save_dir.$mid.'_'.$filename));
		  unlink($save_dir.$mid.'_'.$filename);
		}
	}

	/**
	* @dataProvider provideMails
	*/
	function testGetAttachmentsWithPath($mid, $nbAttachments, $size, $subject){

		$file = __DIR__."/mails/".$mid;

		$Parser = new MimeMailParser();
		$Parser->setPath($file);

		$this->assertEquals($subject,$Parser->getHeader('subject'));

		$attachments = $Parser->getAttachments();

		$this->assertEquals($nbAttachments,count($attachments));

		$save_dir = __DIR__."/mails/";
		foreach($attachments as $attachment) {
		  // get the attachment name
		  $filename = $attachment->filename;
		  // write the file to the directory you want to save it in
		  if ($fp = fopen($save_dir.$mid.'_'.$filename, 'w')) {
		    while($bytes = $attachment->read()) {
		      fwrite($fp, $bytes);
		    }
		    fclose($fp);
		  }
		  $this->assertEquals($size,filesize($save_dir.$mid.'_'.$filename));
		  unlink($save_dir.$mid.'_'.$filename);
		}
	}

	function provideMails(){
		$mails = array(
			array('m0001',1,2, 'Mail avec fichier attaché de 1ko'),
			array('m0002',1,2229, 'Mail avec fichier attaché de 3ko'),
			array('m0003',1,13369, 'Mail de 14 Ko'),
			array('m0004',1,817938, 'Mail de 800ko'),
			array('m0005',1,1635877, 'Mail de 1500 Ko'),
			array('m0006',1,3271754, 'Mail de 3 196 Ko'),
			array('m0007',1,2229, 'Mail avec fichier attaché de 3ko')
		);
		return $mails;
	}
}
?>
