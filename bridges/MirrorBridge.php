<?php
class MirrorBridge extends BridgeAbstract {
	const NAME = 'Mirror.xyz';
	const URI = 'https://mirror.xyz/';
	const DESCRIPTION = 'Subscribe to the Binance blog.';
	const MAINTAINER = 'thefranke';
	const CACHE_TIMEOUT = 3600; // 1h
	const PARAMETERS = array(
		'Ask.address' => array(
			'address' => array(
				'name' => 'Address',
				'required' => true,
				'exampleValue'	=> 'zlexdl.eth'
			)
		)
	);

	public function getIcon() {
		return 'https://mirror.xyz/favicon.ico';
	}

	public function collectData() {
		$html = getSimpleHTMLDOM($this->getURI())
			or returnServerError('Could not fetch Binance blog data.');

		$appData = $html->find('script[id="__NEXT_DATA__"]');
		$appDataJson = json_decode($appData[0]->innertext);

		foreach($appDataJson->props->pageProps->project->posts as $element) {

			$date = $element->latestBlockData->timestamp;
			$title = $element->title;
			$content = markdownToHtml($element->body);
			$_URL = [];
            $filePath = $_SERVER['REQUEST_URI'];
            $filePath_array = explode("/", $filePath);

			$item = array();
			$item['title'] = $title;
			$item['uri'] = '';
			$item['timestamp'] = substr($date, 0, -3);
			$item['author'] = $filePath;
			$item['content'] = $content;

			$this->items[] = $item;

			if (count($this->items) >= 10)
				break;
		}
	}
	public function getURI(){
		if(!is_null($this->getInput('address'))) {
			return self::URI . urlencode($this->getInput('address'));
		}

		return parent::getURI();
	}

}
