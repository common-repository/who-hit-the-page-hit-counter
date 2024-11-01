<?php

// namespace Mahlamusa\Material\Functions;

require_once __DIR__ . '../../src/Lite.php';

use Mahlamusa\Material\Lite;

if ( ! function_exists( 'Scripts' ) ) {
	function Scripts() {
		Lite::scripts();
	}
}

if ( ! function_exists( 'Styles' ) ) {
	function Styles() {
		Lite::styles();
	}
}

if ( ! function_exists( 'Header' ) ) {
	function Header( $title, $args ) {
		return Lite::header( $title, $args );
	}
}

if ( ! function_exists( 'TabBar' ) ) {
	function TabBar( $args ) {
		return Lite::tab_bar( $args );
	}
}

if ( ! function_exists( 'Section' ) ) {
	function Section( $args ) {
		return Lite::section( $args );
	}
}

if ( ! function_exists( 'Tab' ) ) {
	function Tab( $args ) {
		return Lite::tab( $args );
	}
}

if ( ! function_exists( 'Tabs' ) ) {
	function Tabs( $args ) {
		return Lite::tabs( $args );
	}
}

if ( ! function_exists( 'Open' ) ) {
	function Open( $what, $id = '', $class = '' ) {
		return Lite::open( $what, $id = '', $class = '' );
	}
}

if ( ! function_exists( 'Close' ) ) {
	function Close( $what, $how_many = 1 ) {
		return Lite::close( $what, $how_many );
	}
}

if ( ! function_exists( 'Grid' ) ) {
	function Grid() {
		return Lite::grid();
	}
}

if ( ! function_exists( 'Col' ) ) {
	function Col( $size ) {
		return Lite::col( $size );
	}
}

if ( ! function_exists( 'Card' ) ) {
	function Card( $args ) {
		return Lite::card( $args );
	}
}

if ( ! function_exists( 'Input' ) ) {
	function Input( $args ) {
		return Lite::text_field( $args );
	}
}

if ( ! function_exists( 'Text_Area' ) ) {
	function Text_Area( $args ) {
		return Lite::textarea( $args );
	}
}

if ( ! function_exists( 'RangeSelector' ) ) {
	function RangeSelector( $args ) {
		return Lite::range( $args );
	}
}

if ( ! function_exists( 'Progress' ) ) {
	function Progress( $args ) {
		return Lite::progress( $args );
	}
}

if ( ! function_exists( 'Tooltip' ) ) {
	function Tooltip( $args ) {
		return Lite::tooltip( $args );
	}
}

if ( ! function_exists( 'Chip' ) ) {
	function Chip( $args ) {
		return Lite::chip( $args );
	}
}

if ( ! function_exists( 'SwitchButton' ) ) {
	function SwitchButton( $args ) {
		return Lite::switch_button( $args );
	}
}

if ( ! function_exists( 'Select' ) ) {
	function Select( $args ) {
		return Lite::select( $args );
	}
}

if ( ! function_exists( 'Toggle' ) ) {
	function Toggle( $args ) {
		return Lite::toggle( $args );
	}
}

if ( ! function_exists( 'Button' ) ) {
	function Button( $args ) {
		return Lite::button( $args );
	}
}

if ( ! function_exists( 'Hidden' ) ) {
	function Hidden( $args ) {
		return Lite::hidden( $args );
	}
}

if ( ! function_exists( 'ListView' ) ) {
	function ListView( $args ) {
		return Lite::list_view( $args );
	}
}

if ( ! function_exists( 'ListItem' ) ) {
	function ListItem( $args ) {
		return Lite::list_item( $args );
	}
}

if ( ! function_exists( 'BrL' ) ) {
	function BrL( $echo = true ) {
		if ( $echo ) {
			echo '<br />';
		} else {
			return '<br />';
		}
	}
}

