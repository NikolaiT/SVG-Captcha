<?php

/* 
 * This file contains typefaces defined by lines and Bezier curves.
 * 
 * SVGCaptcha assumes that any letter must conform to typical typeface measurments, hence they are completely free from any distortions and linear transformations.
 */

$alphabet = array(
	'y' => array(
		'width' => 347,
		'height' => 381,
		'glyph_data' => array(
			'cubic_splines' => array(
				array(new Point(178, 245), new Point(178, 245), new Point(148, 314), new Point(122, 339)), array(new Point(122, 339), new Point(109, 350), new Point(93, 361), new Point(76, 363)), array(new Point(76, 363), new Point(53, 366), new Point(6, 342), new Point(6, 342)), array(new Point(0, 359), new Point(0, 359), new Point(49, 381), new Point(73, 379)), 
				array(new Point(0, 359), new Point(0, 359), new Point(49, 381), new Point(73, 379)), array(new Point(73, 379), new Point(93, 377), new Point(112, 365), new Point(128, 352)), array(new Point(128, 352), new Point(158, 325), new Point(175, 286), new Point(195, 250)), array(new Point(195, 250), new Point(235, 178), new Point(296, 16), new Point(296, 16)),
				array(new Point(195, 250), new Point(235, 178), new Point(296, 16), new Point(296, 16))
			),
			'lines' => array(
				array(new Point(30, 19), new Point(65, 19)), array(new Point(65, 19), new Point(178, 245)), array(new Point(6, 342), new Point(0, 359)), array(new Point(296, 16), new Point(347, 16)), 
				array(new Point(296, 16), new Point(347, 16)), array(new Point(347, 16), new Point(347, 0)), array(new Point(347, 0), new Point(239, 0)), array(new Point(239, 0), new Point(239, 16)), 
				array(new Point(239, 0), new Point(239, 16)), array(new Point(239, 16), new Point(278, 16)), array(new Point(278, 16), new Point(189, 229)), array(new Point(189, 229), new Point(81, 19)), 
				array(new Point(189, 229), new Point(81, 19)), array(new Point(81, 19), new Point(135, 18)), array(new Point(135, 18), new Point(135, 0)), array(new Point(135, 0), new Point(30, 0)), 
				array(new Point(135, 0), new Point(30, 0)), array(new Point(30, 0), new Point(30, 19))
		))
	),
	'W' => array(
		'width' => 520,
		'height' => 390,
		'glyph_data' => array(
			'lines' => array(
				array(new Point(0, 0), new Point(130, 390)), array(new Point(130, 390), new Point(190, 390)), array(new Point(190, 390), new Point(270, 120)), array(new Point(270, 120), new Point(350, 390)), 
				array(new Point(270, 120), new Point(350, 390)), array(new Point(350, 390), new Point(410, 390)), array(new Point(410, 390), new Point(520, 0)), array(new Point(520, 0), new Point(430, 10)), 
				array(new Point(520, 0), new Point(430, 10)), array(new Point(430, 10), new Point(380, 290)), array(new Point(380, 290), new Point(300, 80)), array(new Point(300, 80), new Point(240, 80)), 
				array(new Point(300, 80), new Point(240, 80)), array(new Point(240, 80), new Point(160, 290)), array(new Point(160, 290), new Point(90, 10)), array(new Point(90, 10), new Point(0, 0)),
				array(new Point(90, 10), new Point(0, 0))
		))
	),
	'G' => array(
		'width' => 248,
		'height' => 353,
		'glyph_data' => array(
			'cubic_splines' => array(
				array(new Point(248, 60), new Point(248, 60), new Point(211, 28), new Point(189, 17)), array(new Point(189, 17), new Point(169, 7), new Point(146, 0), new Point(124, 4)), array(new Point(124, 4), new Point(98, 8), new Point(74, 25), new Point(56, 45)), array(new Point(56, 45), new Point(33, 70), new Point(20, 103), new Point(12, 135)), 
				array(new Point(56, 45), new Point(33, 70), new Point(20, 103), new Point(12, 135)), array(new Point(12, 135), new Point(3, 175), new Point(0, 218), new Point(12, 257)), array(new Point(12, 257), new Point(21, 287), new Point(39, 315), new Point(64, 333)), array(new Point(64, 333), new Point(83, 347), new Point(108, 352), new Point(132, 352)), 
				array(new Point(64, 333), new Point(83, 347), new Point(108, 352), new Point(132, 352)), array(new Point(132, 352), new Point(167, 353), new Point(236, 344), new Point(236, 344)), array(new Point(207, 297), new Point(208, 326), new Point(181, 324), new Point(158, 321)), array(new Point(158, 321), new Point(130, 317), new Point(74, 301), new Point(58, 278)), 
				array(new Point(158, 321), new Point(130, 317), new Point(74, 301), new Point(58, 278)), array(new Point(58, 278), new Point(38, 248), new Point(39, 208), new Point(43, 174)), array(new Point(43, 174), new Point(46, 136), new Point(56, 95), new Point(80, 65)), array(new Point(80, 65), new Point(96, 47), new Point(119, 34), new Point(144, 36)), 
				array(new Point(80, 65), new Point(96, 47), new Point(119, 34), new Point(144, 36)), array(new Point(144, 36), new Point(177, 38), new Point(224, 84), new Point(224, 84)), array(new Point(224, 84), new Point(224, 84), new Point(218, 92), new Point(248, 60))
			),
			'lines' => array(
				array(new Point(236, 344), new Point(238, 202)), array(new Point(238, 202), new Point(118, 200)), array(new Point(118, 200), new Point(116, 231)), array(new Point(116, 231), new Point(207, 231)), 
				array(new Point(116, 231), new Point(207, 231)), array(new Point(207, 231), new Point(207, 297)), array(new Point(248, 60), new Point(248, 60))
		))
	),
	'e' => array(
		'width' => 480,
		'height' => 615,
		'glyph_data' => array(
			'cubic_splines' => array(
				array(new Point(46, 207), new Point(0, 331), new Point(3, 525), new Point(204, 570)), array(new Point(204, 570), new Point(404, 615), new Point(454, 423), new Point(460, 406)), array(new Point(389, 406), new Point(389, 406), new Point(354, 498), new Point(313, 515)), array(new Point(313, 515), new Point(252, 539), new Point(166, 522), new Point(121, 474)), 
				array(new Point(313, 515), new Point(252, 539), new Point(166, 522), new Point(121, 474)), array(new Point(121, 474), new Point(82, 433), new Point(98, 306), new Point(98, 306)), array(new Point(461, 304), new Point(461, 304), new Point(480, 140), new Point(334, 70)), array(new Point(334, 70), new Point(188, 0), new Point(83, 108), new Point(46, 207)), 
				array(new Point(334, 70), new Point(188, 0), new Point(83, 108), new Point(46, 207)), array(new Point(387, 257), new Point(387, 257), new Point(379, 114), new Point(251, 112)), array(new Point(251, 112), new Point(123, 109), new Point(97, 257), new Point(97, 257))
			),
			'lines' => array(
				array(new Point(460, 406), new Point(389, 406)), array(new Point(98, 306), new Point(461, 304)), array(new Point(46, 207), new Point(46, 207)), array(new Point(97, 257), new Point(387, 257)), 
				array(new Point(97, 257), new Point(387, 257)), array(new Point(97, 257), new Point(97, 257))
		))
	),
	'a' => array(
		'width' => 351,
		'height' => 634,
		'glyph_data' => array(
			'cubic_splines' => array(
				array(new Point(195, 0), new Point(163, 0), new Point(133, 9), new Point(107, 27)), array(new Point(107, 27), new Point(71, 53), new Point(60, 162), new Point(60, 162)), array(new Point(60, 162), new Point(73, 177), new Point(95, 212), new Point(95, 212)), array(new Point(95, 212), new Point(95, 212), new Point(104, 99), new Point(129, 72)), 
				array(new Point(95, 212), new Point(95, 212), new Point(104, 99), new Point(129, 72)), array(new Point(129, 72), new Point(161, 31), new Point(207, 26), new Point(262, 74)), array(new Point(262, 74), new Point(300, 130), new Point(277, 185), new Point(245, 228)), array(new Point(245, 228), new Point(209, 277), new Point(151, 274), new Point(105, 287)), 
				array(new Point(245, 228), new Point(209, 277), new Point(151, 274), new Point(105, 287)), array(new Point(105, 287), new Point(9, 326), new Point(0, 444), new Point(23, 521)), array(new Point(23, 521), new Point(32, 562), new Point(53, 590), new Point(90, 601)), array(new Point(90, 601), new Point(150, 618), new Point(193, 601), new Point(225, 563)), 
				array(new Point(90, 601), new Point(150, 618), new Point(193, 601), new Point(225, 563)), array(new Point(225, 563), new Point(231, 590), new Point(232, 620), new Point(258, 626)), array(new Point(258, 626), new Point(298, 634), new Point(351, 628), new Point(312, 589)), array(new Point(312, 589), new Point(273, 551), new Point(283, 535), new Point(281, 510)), 
				array(new Point(312, 589), new Point(273, 551), new Point(283, 535), new Point(281, 510)), array(new Point(335, 71), new Point(339, 43), new Point(291, 17), new Point(240, 5)), array(new Point(240, 5), new Point(224, 1), new Point(209, 0), new Point(195, 0)), array(new Point(252, 283), new Point(270, 367), new Point(251, 535), new Point(152, 571)), 
				array(new Point(252, 283), new Point(270, 367), new Point(251, 535), new Point(152, 571)), array(new Point(152, 571), new Point(54, 608), new Point(35, 434), new Point(72, 384)), array(new Point(72, 384), new Point(124, 313), new Point(178, 279), new Point(252, 283))
			),
			'lines' => array(
				array(new Point(281, 510), new Point(335, 71)), array(new Point(195, 0), new Point(195, 0)), array(new Point(252, 283), new Point(252, 283))
		))
	),
	'H' => array(
		'width' => 420,
		'height' => 550,
		'glyph_data' => array(
			'lines' => array(
				array(new Point(0, 0), new Point(0, 35)), array(new Point(0, 35), new Point(55, 35)), array(new Point(55, 35), new Point(55, 520)), array(new Point(55, 520), new Point(0, 520)), 
				array(new Point(55, 520), new Point(0, 520)), array(new Point(0, 520), new Point(0, 550)), array(new Point(0, 550), new Point(150, 550)), array(new Point(150, 550), new Point(150, 520)), 
				array(new Point(150, 550), new Point(150, 520)), array(new Point(150, 520), new Point(95, 520)), array(new Point(95, 520), new Point(95, 270)), array(new Point(95, 270), new Point(325, 270)), 
				array(new Point(95, 270), new Point(325, 270)), array(new Point(325, 270), new Point(325, 520)), array(new Point(325, 520), new Point(265, 520)), array(new Point(265, 520), new Point(265, 550)), 
				array(new Point(265, 520), new Point(265, 550)), array(new Point(265, 550), new Point(420, 550)), array(new Point(420, 550), new Point(420, 520)), array(new Point(420, 520), new Point(370, 520)), 
				array(new Point(420, 520), new Point(370, 520)), array(new Point(370, 520), new Point(370, 35)), array(new Point(370, 35), new Point(420, 35)), array(new Point(420, 35), new Point(420, 0)), 
				array(new Point(420, 35), new Point(420, 0)), array(new Point(420, 0), new Point(265, 0)), array(new Point(265, 0), new Point(265, 35)), array(new Point(265, 35), new Point(325, 35)), 
				array(new Point(265, 35), new Point(325, 35)), array(new Point(325, 35), new Point(325, 230)), array(new Point(325, 230), new Point(95, 230)), array(new Point(95, 230), new Point(95, 35)), 
				array(new Point(95, 230), new Point(95, 35)), array(new Point(95, 35), new Point(150, 35)), array(new Point(150, 35), new Point(150, 0)), array(new Point(150, 0), new Point(0, 0)),
				array(new Point(150, 0), new Point(0, 0))
		))
	),
	'k' => array(
		'width' => 420,
		'height' => 680,
		'glyph_data' => array(
			'lines' => array(
				array(new Point(0, 0), new Point(60, 0)), array(new Point(60, 0), new Point(60, 490)), array(new Point(60, 490), new Point(350, 280)), array(new Point(350, 280), new Point(420, 280)), 
				array(new Point(350, 280), new Point(420, 280)), array(new Point(420, 280), new Point(210, 440)), array(new Point(210, 440), new Point(420, 680)), array(new Point(420, 680), new Point(350, 680)), 
				array(new Point(420, 680), new Point(350, 680)), array(new Point(350, 680), new Point(170, 470)), array(new Point(170, 470), new Point(60, 550)), array(new Point(60, 550), new Point(60, 680)), 
				array(new Point(60, 550), new Point(60, 680)), array(new Point(60, 680), new Point(0, 680)), array(new Point(0, 680), new Point(0, 0))
		))
	),
	'i' => array(
		'width' => 122,
		'height' => 687,
		'glyph_data' => array(
			'cubic_splines' => array(
				array(new Point(67, 1), new Point(48, 0), new Point(28, 14), new Point(17, 29)), array(new Point(17, 29), new Point(6, 45), new Point(0, 67), new Point(7, 85)), array(new Point(7, 85), new Point(14, 107), new Point(37, 128), new Point(60, 130)), array(new Point(60, 130), new Point(79, 131), new Point(99, 117), new Point(109, 100)), 
				array(new Point(60, 130), new Point(79, 131), new Point(99, 117), new Point(109, 100)), array(new Point(109, 100), new Point(120, 82), new Point(122, 56), new Point(113, 37)), array(new Point(113, 37), new Point(105, 19), new Point(86, 3), new Point(67, 1))
			),
			'lines' => array(
				array(new Point(23, 214), new Point(23, 687)), array(new Point(23, 687), new Point(96, 687)), array(new Point(96, 687), new Point(96, 214)), array(new Point(96, 214), new Point(23, 214)), 
				array(new Point(96, 214), new Point(23, 214)), array(new Point(67, 1), new Point(67, 1))
		))
	),
	'f' => array(
		'width' => 240,
		'height' => 600,
		'glyph_data' => array(
			'cubic_splines' => array(
				array(new Point(240, 0), new Point(240, 0), new Point(167, 0), new Point(138, 11)), array(new Point(138, 11), new Point(106, 24), new Point(84, 48), new Point(70, 80)), array(new Point(70, 80), new Point(57, 108), new Point(60, 170), new Point(60, 170)), array(new Point(90, 170), new Point(90, 170), new Point(87, 116), new Point(97, 91)), 
				array(new Point(90, 170), new Point(90, 170), new Point(87, 116), new Point(97, 91)), array(new Point(97, 91), new Point(106, 68), new Point(146, 48), new Point(170, 40)), array(new Point(170, 40), new Point(197, 31), new Point(240, 50), new Point(240, 50))
			),
			'lines' => array(
				array(new Point(240, 50), new Point(240, 0)), array(new Point(60, 170), new Point(0, 170)), array(new Point(0, 170), new Point(0, 200)), array(new Point(0, 200), new Point(60, 200)), 
				array(new Point(0, 200), new Point(60, 200)), array(new Point(60, 200), new Point(60, 570)), array(new Point(60, 570), new Point(0, 570)), array(new Point(0, 570), new Point(0, 600)), 
				array(new Point(0, 570), new Point(0, 600)), array(new Point(0, 600), new Point(130, 600)), array(new Point(130, 600), new Point(150, 570)), array(new Point(150, 570), new Point(90, 570)), 
				array(new Point(150, 570), new Point(90, 570)), array(new Point(90, 570), new Point(90, 200)), array(new Point(90, 200), new Point(150, 200)), array(new Point(150, 200), new Point(150, 170)), 
				array(new Point(150, 200), new Point(150, 170)), array(new Point(150, 170), new Point(90, 170)), array(new Point(240, 50), new Point(240, 50))
		))
	),
	'b' => array(
		'width' => 237,
		'height' => 454,
		'glyph_data' => array(
			'cubic_splines' => array(
				array(new Point(43, 0), new Point(39, 13), new Point(38, 20), new Point(37, 26)), array(new Point(37, 26), new Point(0, 302), new Point(5, 438), new Point(5, 438)), array(new Point(5, 438), new Point(5, 438), new Point(142, 454), new Point(188, 414)), array(new Point(188, 414), new Point(222, 385), new Point(237, 329), new Point(224, 287)), 
				array(new Point(188, 414), new Point(222, 385), new Point(237, 329), new Point(224, 287)), array(new Point(224, 287), new Point(213, 254), new Point(177, 221), new Point(141, 220)), array(new Point(141, 220), new Point(99, 220), new Point(40, 295), new Point(40, 295)), array(new Point(69, 305), new Point(69, 305), new Point(18, 373), new Point(38, 398)), 
				array(new Point(69, 305), new Point(69, 305), new Point(18, 373), new Point(38, 398)), array(new Point(38, 398), new Point(64, 431), new Point(131, 416), new Point(161, 388)), array(new Point(161, 388), new Point(186, 366), new Point(189, 321), new Point(178, 289)), array(new Point(178, 289), new Point(172, 272), new Point(156, 253), new Point(138, 253)), 
				array(new Point(178, 289), new Point(172, 272), new Point(156, 253), new Point(138, 253)), array(new Point(138, 253), new Point(109, 251), new Point(69, 305), new Point(69, 305))
			),
			'lines' => array(
				array(new Point(40, 295), new Point(85, 4)), array(new Point(85, 4), new Point(43, 0)), array(new Point(69, 305), new Point(69, 305))
		))
	),
	'n' => array(
		'width' => 420,
		'height' => 380,
		'glyph_data' => array(
			'cubic_splines' => array(
				array(new Point(111, 50), new Point(111, 50), new Point(146, 38), new Point(206, 39)), array(new Point(206, 39), new Point(267, 41), new Point(287, 53), new Point(304, 67)), array(new Point(304, 67), new Point(318, 79), new Point(340, 110), new Point(340, 110)), array(new Point(370, 110), new Point(370, 110), new Point(361, 71), new Point(340, 50)), 
				array(new Point(370, 110), new Point(370, 110), new Point(361, 71), new Point(340, 50)), array(new Point(340, 50), new Point(310, 20), new Point(288, 15), new Point(220, 10)), array(new Point(220, 10), new Point(151, 5), new Point(110, 20), new Point(110, 20))
			),
			'lines' => array(
				array(new Point(30, 0), new Point(30, 30)), array(new Point(30, 30), new Point(80, 30)), array(new Point(80, 30), new Point(80, 350)), array(new Point(80, 350), new Point(30, 350)), 
				array(new Point(80, 350), new Point(30, 350)), array(new Point(30, 350), new Point(0, 380)), array(new Point(0, 380), new Point(160, 380)), array(new Point(160, 380), new Point(160, 350)), 
				array(new Point(160, 380), new Point(160, 350)), array(new Point(160, 350), new Point(110, 350)), array(new Point(110, 350), new Point(111, 50)), array(new Point(340, 110), new Point(340, 350)), 
				array(new Point(340, 110), new Point(340, 350)), array(new Point(340, 350), new Point(290, 350)), array(new Point(290, 350), new Point(290, 380)), array(new Point(290, 380), new Point(420, 380)), 
				array(new Point(290, 380), new Point(420, 380)), array(new Point(420, 380), new Point(370, 350)), array(new Point(370, 350), new Point(370, 110)), array(new Point(110, 20), new Point(110, 0)), 
				array(new Point(110, 20), new Point(110, 0)), array(new Point(110, 0), new Point(30, 0))
		))
	),
	'S' => array(
		'width' => 354,
		'height' => 745,
		'glyph_data' => array(
			'cubic_splines' => array(
				array(new Point(287, 366), new Point(250, 289), new Point(141, 264), new Point(99, 189)), array(new Point(99, 189), new Point(88, 168), new Point(78, 141), new Point(85, 118)), array(new Point(85, 118), new Point(92, 96), new Point(116, 82), new Point(135, 71)), array(new Point(135, 71), new Point(157, 58), new Point(182, 50), new Point(207, 48)), 
				array(new Point(135, 71), new Point(157, 58), new Point(182, 50), new Point(207, 48)), array(new Point(207, 48), new Point(244, 44), new Point(288, 42), new Point(319, 63)), array(new Point(319, 63), new Point(335, 73), new Point(348, 128), new Point(347, 110)), array(new Point(346, 44), new Point(345, 21), new Point(354, 16), new Point(293, 15)), 
				array(new Point(346, 44), new Point(345, 21), new Point(354, 16), new Point(293, 15)), array(new Point(293, 15), new Point(293, 15), new Point(161, 0), new Point(88, 58)), array(new Point(88, 58), new Point(16, 116), new Point(27, 169), new Point(39, 196)), array(new Point(39, 196), new Point(74, 277), new Point(183, 304), new Point(233, 377)), 
				array(new Point(39, 196), new Point(74, 277), new Point(183, 304), new Point(233, 377)), array(new Point(233, 377), new Point(248, 400), new Point(256, 427), new Point(262, 454)), array(new Point(262, 454), new Point(270, 493), new Point(272, 533), new Point(267, 572)), array(new Point(267, 572), new Point(261, 609), new Point(265, 640), new Point(228, 679)), 
				array(new Point(267, 572), new Point(261, 609), new Point(265, 640), new Point(228, 679)), array(new Point(228, 679), new Point(182, 727), new Point(84, 731), new Point(28, 695)), array(new Point(28, 695), new Point(6, 681), new Point(0, 604), new Point(1, 623)), array(new Point(3, 691), new Point(6, 745), new Point(67, 742), new Point(94, 742)), 
				array(new Point(3, 691), new Point(6, 745), new Point(67, 742), new Point(94, 742)), array(new Point(94, 742), new Point(124, 741), new Point(153, 741), new Point(182, 741)), array(new Point(182, 741), new Point(235, 740), new Point(287, 664), new Point(307, 605)), array(new Point(307, 605), new Point(333, 530), new Point(322, 438), new Point(287, 366)),
				array(new Point(307, 605), new Point(333, 530), new Point(322, 438), new Point(287, 366))
			),
			'lines' => array(
				array(new Point(347, 110), new Point(346, 44)), array(new Point(1, 623), new Point(3, 691)), array(new Point(287, 366), new Point(287, 366))
		))
	),
	'X' => array(
		'width' => 300,
		'height' => 400,
		'glyph_data' => array(
			'lines' => array(
				array(new Point(10, 0), new Point(130, 200)), array(new Point(130, 200), new Point(0, 390)), array(new Point(0, 390), new Point(40, 390)), array(new Point(40, 390), new Point(150, 220)), 
				array(new Point(40, 390), new Point(150, 220)), array(new Point(150, 220), new Point(260, 400)), array(new Point(260, 400), new Point(300, 400)), array(new Point(300, 400), new Point(170, 190)), 
				array(new Point(300, 400), new Point(170, 190)), array(new Point(170, 190), new Point(296, 0)), array(new Point(296, 0), new Point(260, 0)), array(new Point(260, 0), new Point(150, 170)), 
				array(new Point(260, 0), new Point(150, 170)), array(new Point(150, 170), new Point(50, 0)), array(new Point(50, 0), new Point(10, 0))
		))
	),
	'E' => array(
		'width' => 370,
		'height' => 680,
		'glyph_data' => array(
			'lines' => array(
				array(new Point(0, 0), new Point(0, 50)), array(new Point(0, 50), new Point(50, 50)), array(new Point(50, 50), new Point(50, 630)), array(new Point(50, 630), new Point(0, 630)), 
				array(new Point(50, 630), new Point(0, 630)), array(new Point(0, 630), new Point(0, 680)), array(new Point(0, 680), new Point(370, 680)), array(new Point(370, 680), new Point(370, 550)), 
				array(new Point(370, 680), new Point(370, 550)), array(new Point(370, 550), new Point(320, 550)), array(new Point(320, 550), new Point(320, 630)), array(new Point(320, 630), new Point(100, 630)), 
				array(new Point(320, 630), new Point(100, 630)), array(new Point(100, 630), new Point(100, 360)), array(new Point(100, 360), new Point(280, 360)), array(new Point(280, 360), new Point(280, 310)), 
				array(new Point(280, 360), new Point(280, 310)), array(new Point(280, 310), new Point(100, 310)), array(new Point(100, 310), new Point(100, 50)), array(new Point(100, 50), new Point(320, 50)), 
				array(new Point(100, 50), new Point(320, 50)), array(new Point(320, 50), new Point(320, 130)), array(new Point(320, 130), new Point(370, 130)), array(new Point(370, 130), new Point(370, 0)), 
				array(new Point(370, 130), new Point(370, 0)), array(new Point(370, 0), new Point(0, 0))
		))
	),
	'Q' => array(
		'width' => 510,
		'height' => 600,
		'glyph_data' => array(
			'cubic_splines' => array(
				array(new Point(70, 90), new Point(68, 202), new Point(71, 408), new Point(70, 440))
			),
			'lines' => array(
				array(new Point(0, 450), new Point(70, 540)), array(new Point(70, 540), new Point(360, 540)), array(new Point(360, 540), new Point(410, 600)), array(new Point(410, 600), new Point(500, 600)), 
				array(new Point(410, 600), new Point(500, 600)), array(new Point(500, 600), new Point(440, 530)), array(new Point(440, 530), new Point(510, 460)), array(new Point(510, 460), new Point(510, 70)), 
				array(new Point(510, 460), new Point(510, 70)), array(new Point(510, 70), new Point(431, 2)), array(new Point(431, 2), new Point(70, 0)), array(new Point(70, 0), new Point(0, 70)), 
				array(new Point(70, 0), new Point(0, 70)), array(new Point(0, 70), new Point(0, 450)), array(new Point(70, 440), new Point(110, 480)), array(new Point(110, 480), new Point(310, 480)), 
				array(new Point(110, 480), new Point(310, 480)), array(new Point(310, 480), new Point(270, 420)), array(new Point(270, 420), new Point(360, 420)), array(new Point(360, 420), new Point(400, 480)), 
				array(new Point(360, 420), new Point(400, 480)), array(new Point(400, 480), new Point(440, 430)), array(new Point(440, 430), new Point(440, 90)), array(new Point(440, 90), new Point(390, 50)), 
				array(new Point(440, 90), new Point(390, 50)), array(new Point(390, 50), new Point(120, 50)), array(new Point(120, 50), new Point(70, 90))
		))
	),
);
