<?php
/**
 * Array nesnesi görevini gören Class.
 * @deprecated v0.0.1
 * @link http://www.kodkutuphanesi.com/
 * @author kalaomer <kalaomer@hotmail.com>
 */

/*
 * New array functions adding.
 */
require_once "addition_array_functions.php";

class EasyArray {
	
	/**
	 * Class veriyonu
	 * @var string
	 */
	public static $ver = "0.0.1";
	
	/**
	 * Bağımlı olduğu yapı(lar).
	 */
	public static $dependent_modules = array();

	/**
	 * Array için tutulması istenen verinin tutulduğu yer.
	 * @var array
	 */
	protected $data = array();

	/**
	 * Nesnenin çocuk olup olmadığını belirtir.
	 */
	protected $is_child = false;

	/**
	 * Yol cümlesi ayıracı.
	 */
	public $path_splitter = ".";

	/**
	 * Arr nesnelerini otomatik algılayıp toArray() ile array'a çevirme özelliğini belirtir.
	 * Bu işlem çok performans tükettiği için manuel hale getirilmiştir.
	 * (En az yaklaşık 10 katı performans tüketimi)
	 */
	public static $check_arr_objs = false;
	
	/**
	 * Buradaki fonksyionlar ard arda eklenen array'ları kullanan fonksiyonlardır.
	 * Ör: $a->function( array, array, array );
	 * @var array
	 */
	public static $array_funcs_1 = array(
		"array_key_joiner",
		"array_val_joiner",
		"array_merge",
		"array_merge_recursive"
	);
	
	/**
	 * Bu fonksiyonlar içine class'ın array'ını ve (alırsa) bazı argümanlar alıp değer üretir.
	 * Ör: f( array, ... );
	 * Array'da değişiklik yapabilirler, çıktıları yeni array değildir.
	 * @var array
	 */
	public static $array_funcs_2 = array(
		"array_shift",
		"array_unshift",
		"array_pop",
		"array_push",
		"array_sum",
		"array_multisort",
		"array_rand",
		"array_values",
		"array_keys",
		"array_flip",
		"array_unique",
		"array_walk",
		"array_walk_recursive",
		"array_set_by_dot",
		"array_get_by_dot"
	);
	
	/**
	 * Bu fonksyionların çıktısı Asıl array'ın yeni halidir.
	 * Girdi olarak asıl array ve ayar değişkenler vardır.
	 * @var array
	 */
	public static $array_funcs_3 = array(
		"array_reverse",
		"array_slice"
	);

	public function __construct( &$data = array(), $is_child = false ) {

		if ( self::$check_arr_objs ) {
			$data = $this->arr_to_array( $data );
		}

		if( !is_array( $data ) )
			$data = array( $data );
		
		if( $is_child )
			$this->data = &$data;
		else
			$this->data = $data;

	}
	
	/**
	 * Bir veri çekilmek istendiğinde çalışır.
	 * @param string $name
	 * @return Easy_Array
	 */
	public function __get($name) {
		/*
		 * Eğer çekilmek istenen key yok ise boş array döndür,
		 * Böylece seri bir şekilde array ağacı oluşturulur.
		 * Ör: $ar = ar(); $ar->a->b->c = "d";
		 */
		if( !isset( $this->data[ $name ] ) )
			$this->data[ $name ] = array();
		
		//	Eğer çıkrı Array ise çocuk Array oluştur.
		if ( is_array($this->data[ $name ]) ) {
			return $this->create_child($name);
		} else {
			return $this->data[ $name ];
		}
	}
	
	public function __set($name, $value) {
		$this->key_joiner( array( $name => $value ) );
	}

	public function __isset( $key ) {
		return isset( $this->data[ $key ] );
	}
	
	/**
	 * by_keys fonksiyonunu tetikler.
	 */
	public function __invoke() {
		return $this->by_keys(func_get_args());
	}
	
	public function __call($name, $args) {
		
		/*
		 * Başına array_ eklenerek mevcut fonksiyon isimlerine benzetiliyor.
		 */
		$name = "array_" . $name;
		
		/*
		 * 1. grup fonksiyon
		 * Örnek kullanım; f( array, array, array )
		 */
		if( array_search($name, self::$array_funcs_1 ) !== false ) {
			if( $args == array() )
				trigger_error ("Arguments Error!");

			foreach ($args as $arg) {
				
				//	Eğer argüman obje..
				if(is_object($arg)) {
					//	Eğer obje ARR nesnesi ise..
					if ( get_class($arg) == get_called_class() ) {
						$array = $arg->toArray();
					} else {
						$array = (array) $arg;
					}
				} else {
					$array = $arg;
				}

				$this->change_data( $name($this->data, $array) );
			}

			return $this;
		}
		/*
		 * 2. grup fonksiyonlar, argümana ihtiyacı var veya yok, Ana Array'ı kullanır ve direk değiştir.
		 */
		elseif( array_search($name, self::$array_funcs_2) !== false ) {
			/*
			 * Burada referans ile gönderilmesi önemli bir nokta!
			 */
			$_args = array( &$this->data );
			if( $args != array() )
				foreach ($args as $arg) {
					$_args[] = $arg;
				}
			return call_user_func_array($name, $_args);
		}
		/*
		 * 3. grup fonksiyonlar, argüman ihtiyacı var veya yok, Ana array'ı değiştir.
		 */
		elseif( array_search($name, self::$array_funcs_3) !== false ) {
			array_unshift($args, $this->data);
			$this->change_data( call_user_func_array($name, $args) );
			return $this;
		}
		return trigger_error("Function Name Error!");
	}
	
	/**
	 * Array'yı Class'tan Array'a döker.
	 */
	public function toArray() {
		return $this->data;
	}

	/**
	 * Data değişkenini değiştiren fonksiyon.
	 *
	 * Veri düzeltilmesi esnasında yollanan veri taranır
	 * ve içinde ARR var ise array'a çevrilir.
	 * 
	 * $func değişkeni data'nın nasıl değiştireleceğini belirler.
	 * 
	 * @param array $_data
	 * @param string $func
	 */
	private function change_data( $_data, $func = "" ) {
		
		/*
		 * Eklenecek data taranır, eğer içinde BANGEE ARR var ise, Array'a çevrilir.
		 */
		if ( self::$check_arr_objs ) {
			$_data = $this->arr_to_array( $_data );
		}
		
		/*
		 * Eğer fonksiyon yollanmadıysa direk eşitle,
		 * yollandıysa fonksiyonu çalıştır.
		 */
		if( $func == "" )
			$this->data = $_data;
		else
			$this->data = call_user_func_array ($func, array($this->data, $_data) );

	}

	/**
	 * Çocuk oluşturucu.
	 * @param array $data
	 * @return Easy_Array
	 */
	private function create_child( $key ) {
		return new self( $this->data[$key], true );
	}

	/**
	 * Eklenecek data taranır, eğer içinde BANGEE ARRAY var ise, Array'a çevrilir.
	 */
	private function arr_to_array($_data) {
		array_walk_recursive($_data, function( &$__data ) {
			if(is_object($__data) && get_class($__data) == get_called_class())
				$__data = $__data->toArray();
		});
		return $_data;
	}

	/**
	 * $arr->get_by_dot(...) fonksiyonunun kısa ulaşım şeklidir.
	 */
	public function get() {
		return call_user_func_array(array( $this, "get_by_dot" ), func_get_args());
	}

	/**
	 * $arr->set_by_dot(...) fonksiyonunun kısa ulaşım şeklidir.
	 */
	public function set() {
		return call_user_func_array(array( $this, "set_by_dot" ), func_get_args());
	}
	
	/**
	 * En sona eleman(lar) ekler.
	 * Diğer deyişle Push işlemini uygular.
	 * 
	 * Çoklu eleman ekleyebilir.
	 * Ör: $a->add( 1,2,3 );
	 */
	public function add() {
		$args = func_get_args();
		if( $args == array() )
			trigger_error ("Argument Error!");
		
		/*
		 * Change_data ile sürekli array_merge'nin çalıştırılması performansı çok etkilemekteydi.
		 * Direk olarak data değiştirme burada gerçekleştirilerek yaklaşık 10 katı işlem hızlandırma yapıldı.
		 */
		foreach ($args as $value) {
			$this->data[] = $value;
		}
		
		return $this;
	}

	/**
	 * Belirtilen yerden itibaren elemanları ekler.
	 * Diğerlerinin sırasını kaydırır.
	 * 
	 * Çoklu eleman ekleyebilir.
	 * Ör: $a->add_to( 3, "a", "b" );
	 */
	public function add_to() {

		$args = func_get_args();

		/*
		 * Yollanan argüman sayısına bakılıyor, en az 2 olmalı!
		 */
		if( !isset( $args[1] ) )
			return trigger_error("Miss Ergument!");

		/*
		 * Yollanan level değeri sayı olmalı.
		 */
		$level = array_shift($args);
		if( !is_int( $level ) )
			return trigger_error("Level must be INT!");

		foreach ($args as $key => $val) {
			if( isset( $this->data[ $level + $key ] ) ) {
				$_val = $this->data[ $level + $key ];
				$this->add_to( $level + $key + 1, $_val );
			}
			$this->data[ $level + $key ] = $val;
		}

		return $this;
	}
	
	
	/**
	 * Gönderilen key'lere göre içerikten elemanları unset() eder.
	 * @return Easy_Array
	 */
	public function kill() {
		$args = func_get_args();

		/*
		 * Eğer kill() fonksiyonuna girdi yollanmadıysa nesne data'sını sil.
		 */
		if( $args == array() ) {
			$this->data = array();
			return $this;
		}
		
		/*
		 * Change_data() kullanımı kaldırılarak performansa yönelik değişiklik yapıldı.
		 */
		foreach ($args as $arg) {
			unset( $this->data[ $arg ] );
		}
		
		return $this;
	}
	
	/**
	 * Aile'den bağımsız bir eleman oluşturur.
	 * @return Easy_Array
	 */
	public function copy() {
		return new self( $this->data, false );
	}

	/**
	 * Bir int key'e sahip veriyi taşır.
	 * @return Easy_Array
	 */
	public function move_key( $key, $to ) {
		if( !is_int( $to ) )
			return trigger_error( "Moving place must be Int!" );

		$data = $this->data[ $key ];
		$this->kill( $key );

		return $this->add_to( $to, $data );
	}

	/**
	 * Array_walk'ın takma adı.
	 * @return void
	 */
	public function each( $f ) {
		$this->walk( $f );
	}
	
	/**
	 * Gönderilen key'leri Array nesnesi yapıp döndürür.
	 * Çocuk grubu oluşturur.
	 *
	 * @return Easy_Array
	 */
	public function by_keys() {
		if ( ($args = func_get_args()) == array() )
			trigger_error ("Argument Error!");
		
		$result = array();
		
		//	Referansı alınıp yeni nesne oluştur,
		//	böylece bağı koparma.
		foreach ($args as $arg) {
			$result[] = &$this->data[ $arg ];
		}
		
		return new self($result);
	}
	
	public function search($needle) {
		return array_search($needle, $this->data);
	}
	
	/**
	 * $data için isset() fonksiyonu.
	 * @param mixed $key
	 * @return true|false
	 */
	public function is_key( $key ) {
		return isset( $this->data[ $key ] );
	}
	
	/**
	 * Count fonksiyonu.
	 * @return int
	 */
	public function count() {
		return count( $this->data );
	}
	
	/**
	 * En baştan belirtilen eleman sayısınca çocuk döndürür.
	 * @param type $length
	 * @param type $preserve_keys
	 * @return Easy_Array
	 */
	public function first( $length = 1, $preserve_keys = false ) {
		$_ = $this->slice( 0, $length, $preserve_keys );
		return new self( $_ );
	}
	
	/**
	 * En sondan belirtilen eleman sayısınca çocuk döndürür.
	 * @param type $length
	 * @param type $preserve_keys
	 * @return Easy_Array
	 */
	public function last( $length = 1, $preserve_keys = false ) {
		$_ = array_reverse( $this->slice( - $length, $length, $preserve_keys )->toArray(), true );
		return new self( $_ );
	}
	
	
}

return "Hello World!";