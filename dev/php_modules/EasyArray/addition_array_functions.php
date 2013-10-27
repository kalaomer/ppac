<?php

/**
 * Temel iki array birleştirme fonksiyonu
 */
if (!function_exists("array_val_joiner")) {
	function array_val_joiner() {
		$args = func_get_args();

		if( count( $args ) < 2 )
			return false;

		$template = array_shift( $args );
		$array = array_shift( $args );

		/*
		 * Eğer gönderilenler array değilse çık!
		 */
		if( !is_array( $template ) || !is_array( $array ) )
			return false;

		/*
		 * Eğer gönderilenler iki elamamdan fazlaysa;
		 * fonksiyonu içten içe yönlendir ve array'ı topla.
		 */
		if( count( $args ) > 0 ) {
			array_unshift( $args, $array );
			$array = call_user_func_array( __FUNCTION__, $args );
			if( $array == false )
				return false;
		}

		/*
		 * Arrayda olmayanlar template'den aktarılıyor.
		 */
		foreach( $template as $key=>$value ) {
			if( array_search( $value, $array ) === false )
				if( array_key_exists( $key, $array ) === false )
					$array[ $key ] = $value;
				else
					$array[] = $value;
		}

		return $array;
	}
}

/*
 * Gönderilen taslak array ile normal array'ı kıyaslar ve normal array'a eksik olan key'leri ekler.
 *
 * Birden fazla array gönderilebilir. En baskın array en son yazılan array'dır!!!
 */
if (!function_exists("array_key_joiner")) {
	function array_key_joiner() {
		$args = func_get_args();

		if( count( $args ) < 2 )
			return trigger_error("Argument miss!");

		/*
		 * Template üstüne veri eklenecek olan array.
		 */
		$template = array_shift( $args );

		/*
		 * Eğer gönderilenler array değilse çık!
		 */
		if( !is_array( $template ) )
			return trigger_error("Argument is not Array!");

		/*
		 * Eğer gönderilenler iki elamamdan fazlaysa;
		 * fonksiyonu içten içe yönlendir ve primary değişkenini tek bir array haline getir.
		 */
		if( count( $args ) > 1 ) {
			$primary = call_user_func_array( __FUNCTION__, $args );
			if( $primary == false )
				return false;
		} else {
			if( !is_array($primary = array_shift( $args )) )
				return trigger_error("Argument is not Array!");
		}


		/*
		 * Primary'daki bütün veriler Template'e aktarılıyor.
		 */
		foreach( $primary as $key => $val ) {
			$template[ $key ] = $val;
		}

		return $template;

	}
}

if (!function_exists("array_key_joner_recursive")) {
	function array_key_joner_recursive() {
		$args = func_get_args();

		if( count( $args ) < 2 )
			return trigger_error("Argument miss!");

		/*
		 * Template üstüne veri eklenecek olan array.
		 */
		$template = array_shift( $args );

		/*
		 * Eğer gönderilenler iki elamamdan fazlaysa;
		 * fonksiyonu içten içe yönlendir ve primary değişkenini tek bir array haline getir.
		 */
		if( count( $args ) > 1 ) {
			$primary = call_user_func_array( __FUNCTION__, $args );
			if( $primary == false )
				return false;
		} else {
			if( !is_array($primary = array_shift( $args )) )
				return trigger_error("Argument is not Array!");
		}

		foreach ($primary as $key => $val) {
			/*
			 * Eğer eşitlenecek öğe Array ise ve
			 * Template array'ında bu değer var ise ve
			 * Template array'ında bu key de array bulunduruyorsa
			 * Fonksiyonu iç içe çağır.
			 */
			if( is_array($val) && isset( $template[ $key ] ) && is_array( $template[ $key ] ) )
				$template[ $key ] = call_user_func_array( __FUNCTION__, array( $template[$key], $primary[$key] ) );
			else
				$template[ $key ] = $val;
		}

		return $template;
	}
}

/**
 * Gelişmiş bir veri çekme fonksiyonudur.
 * @param type $path
 * @param type $default_return Eğer yol bulunamaz ise dönülecek olan değer.
 * @param type $parse_default_return Eğer default_return değeri kullanılacak ise, data ya verinin eklenip eklenmeceğini gösteren parametre.
 * @return Data'daki hedef veriyi tipi.
 */
if (!function_exists("array_get_by_dot")) {
	function array_get_by_dot( &$data, $path, $default_return = false, $parse_default_return = false ) {

		//	Yol Parçalanıyor.
		$path = explode(".", $path);

		//	Hedef olarak ile data'yı seç.
		$target_data = &$data;

		//	Yolları tek tek git.
		foreach ($path as $key => $way) {
			
			//	Eğer gidilecek yol yok ise..
			if (!isset($target_data[$way])) {
				
				//	default değerin basılması isteniyor ise..
				if ( $parse_default_return ) {

					/*
					 * Yeni bir kısa döngü oluşturulup hızlı çözüm amaçlandı;
					 * Ondan dolayı kod uzatıldı..
					 */

					//	Gidilecek yol miktarı.
					$path_count = count( $path );

					//	Data'da olmayan yol açılıyor.
					for ($i = $key; $i < $path_count - 1; $i++) {
						$new_way = $path[ $i ];
						$target_data[ $new_way ] = [];

						$target_data = &$target_data[ $new_way ];
					}

					//	En son yol alınıyor.
					$new_way = $path[ $path_count - 1 ];
					
					//	En son yola değer basılıp döndürülüyor.
					return $target_data[ $new_way ] = $default_return;

				}
				//	default basılması isstenmiyorsa ise..
				else {
					//	Sadece default değerini döndür.
					return $default_return;
				}
			}
			
			//	Yolu aşama aşama git ve hedefi daralt.
			$target_data = &$target_data[$way];
		}

		return $target_data;
	}
}

/**
 * Array'ın path'deki yerini değiştiren fonksiyondur.
 * @return $val
 */
if (!function_exists("array_set_by_dot")) {
	function array_set_by_dot( &$data, $path, $val ) {
		
		//	Yol Parçalanıyor.
		$path = explode(".", $path);
		
		//	En son değiştirilecek olan yerin adresi.
		$target = array_pop($path);
		
		$target_data = &$data;

		foreach ($path as $way) {
			if (!isset($target_data[$way]))
				$target_data[ $way ] = [];
			$target_data = &$target_data[$way];
		}

		return $target_data[ $target ] = $val;
	}
}

/**
 * Array oluşturmayı kısa yoldan yaptıran fonksiyon.
 * @example ar( 1,2,3 );
 * @return Easy_Array
 */
function ar() {
	$_ = func_get_args();
	return new EasyArray( $_ );
}

/**
 * Yollanan array'ı \BG\ARR olarak döndürür.
 * @example arr( array( 1,2,3,4=>array(5,6,7) ) );
 * @param array $_
 * @return Easy_Array
 */
function arr( array $_ ) {
	return new EasyArray( $_ );
}

/**
 * Yollanan array'ı \BG\ARR olarak döndürür.
 * Temel özelliği yollanan array değişkenini referans olarak alması.
 * @example arr_ref( $array );
 * @param array $_
 * @return Easy_Array
 */
function arr_ref( array &$_ ) {
	return new Easy_Array( $_ );
}

/**
 * \BG\ARRAY nesnesi iste true döndürür.
 * @param mixed $_
 * @return true|false
 */
function is_ar( $_ ) {
	return method_exists($_, "Easy_Array");
}