jQuery(document).ready(function($) {
	$('input.stars').rating({
		min: 0,
		max: 10,
		step: 1,
		stars: 10,
		size: 'xs',
		starCaptions: {
			1: '1 star',
			2: '2 stars',
			3: '3 stars',
			4: '4 stars',
			5: '5 stars',
			6: '6 stars',
			7: '7 stars',
			8: '8 stars',
			9: '9 stars',
			10: '10 stars',
		},
		clearCaption: '0 Starts'
	});
	$('input.starz').rating({
	displayOnly: true,
	stars: 6,
	max: 10,
	size: 'xs',
	starCaptions: {
		1: '1 star',
		2: '2 stars',
		3: '3 stars',
		4: '4 stars',
		5: '5 stars',
	},
	clearCaption: '0 Starts'
});
});
