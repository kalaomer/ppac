# PPAC ( PHP PACKAGE LOADER )
### TR Anlatım

PPAC'in yapımında tamamen node.js'den esinlenilmiştir. Çalışma prensipleri çok benzerdir bunun için.

PPAC'in asıl amacı paketler halindeki PHP kodlarına kolay ulaşımı sağlamaktır. Paketlere en basit şekilde ulaşmak, onları kullanmak ve güncellemek(ileriki veriyonda) PPAC'in yazılmasındaki asıl amaçtır. Ayrıca Hosting gibi yapılarda da çalışması ve performans olarak gayet tatmin edici olması PPAC'i güçlü yapan özelliklerindendir.

PPAC'in yazılmasında node.js'den esinlenildiği iletilmişti. Node.js'de bulunan node_modules klasörü ve işlevi aynı şekilde php_modules adlı klasörde PPAC'de de uygulanmıştır. Buna ek olarak PPAC'in kurulu olduğu dizinde ortak bir modül havuzu yer almaktadır aynı node.js'deki gibi.

PPAC'in şu an için sadece PACKAGE_INCLUDER'ı vardır. Yani sadece havuzlardan modül veya dosyaları yüklenebilmektedir. İleri ki safhalarda internet üzerinde oluşturulacak ortak bir havuzdan PHP paketleri indirmek mümkün olacaktır.(Projeye gösterilecek destek ile karar verilecek bir aşama)

### PPAC kurulumu
PPAC kurulumu gayet basittir. 'dev' klasörü içindeki dosyaları istediğiniz bir yere kopyalamanız yeterlidir.

### PPAC ile Paket eklemek
Önce PPAC'i require ile eklemeniz gerekir.
```
require ".../ppac/includer.php";
```
Daha sonrasında PPAC ile yüklü olan bir paketi çağırmak için
```PPAC::add( $paketYolu )```
kullanılır.

Örneğin PPAC içinde hazırda olarak verilen EasyArray'ı eklemek için ```PPAC::add("EasyArray")``` denmesi yeterlidir. PPAC otomatik olarak projedeki ve ortak alandaki havuzu tarayarak EasyArray paketini arar ve bulup ekler.

##### Dosyayı Tekrar Ekle
PPAC default olarak ekli olan dosyayı tekrar eklemez. Bunu ```config.php``` dosyasından "php_modules=>common=>module=>reloadble" adresinindeki değeri ```true``` yaparak, dosyaları tekrar tekrar yükleme özelliğini default yapabilirsiniz. Eğer default yapmadan anlık olarak tekrar yükletmek istiyorsanız:
```
PPAC::add( $paketYolu, array("reloadable"=>true) )
```
şeklinde belirtmeniz gerekir.

##### Require Sonucunu Almak
PPAC default olarak return işleminin sonucunu döndürmemektedir. Dosya eklendiyse ```true```, eklenmediyse ```false``` dönmektedir. Bunu ```config.php``` dosyasından "php_modules=>common=>module=>returnRequire" adresinindeki değeri ```true``` yaparak, require çıktısını döndürme özelliğini default yapabilirsiniz. Eğer default yapmadan anlık olarak tekrar yükletmek istiyorsanız:
```
PPAC::add( $paketYolu, array("returnRequire"=>true) )
```
şeklinde belirtmeniz gerekir.

##### Önce Ortak Havuz da Arama Yaptırma
PPAC normalde önce proje içinde arama yapar, en son ortak havuza bakar. Bazı projelerde ortak havuzu direk aramak daha mantıklı olabilir. Bu özelliği ```config.php``` dosyasından "php_modules=>public=>addFirst" adresinindeki değeri ```true``` yaparak değiştirebilirsiniz. Eğer sadece o proje için değiştirmek istiyorsanız:
```
PPAC::config( "php_module.public.addFirst", true )
```
şeklinde belirtmeniz gerekir.

### PPAC'e Otomatik Paket Yükleme Yaptırmak
PPAC ile ```::add(...)``` fonksiyonunu kullanmadan paket veya dosya eklemek de mümkündür. Bunun için ```spl_autoload_register()``` fonksiyonu ile PPAC kendisini otomatik olarak yetkilendirir(Bunu değiştirmek için ```config.php``` `dosyasından "php_modules=>common=>autoLoadClass" adresinindeki değeri düzenleyebilirsiniz). Bu sayede herhangi bir fazladan işlem yapmadan otomatik olarak yükleme yaptırabilirsiniz. Ör:
```
//	PPAC eklendi.
require ".../ppac/includer.php";

//	Otomatik olarak PPAC::add("someModule\\Core\\Base"); fonksiyonu tetiklendi.
$some = new someModule\Core\Base;
```

### PPAC Çalışma Prensibi
PPAC, node.js'deki ```require``` sistemi ile aynı şekilde çalışmaktadır(bkz. http://nodejs.org/api/modules.html#modules_loading_from_node_modules_folders).

Öncelikle yüklü olan paketlerin belirli bir standartta olması gerekmektedir(https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md). PPAC ilk require işlemi ile eklendiğinde(```require ".../ppac/includer.php"```) eklendiği dosyanın klasörünü tavan noktası olarak ele alır. Bu değer havuz ararken sınır olarak kullanılır. Daha sonra PPAC'den modül veya Class yüklenmesi istediğinde, önce modül adresi belirlenir. Bu isteğin yollandığı dosya taban olarak kabul edilir. Sonrasında eklenmek istenen öğenin paket adresi eğer bilinmiyor ise taban ile tavan arasındaki havuzlara bakılır. Eğer orada bulunamaz ise sonrasında ortak havuza bakılır. Modül yolu bu şekilde aranır.

Sonrasında Modül'deki çağrılacak dosya belirlenir. Bunun için namespace'den yararlanılır. Eğer çağrılan şey modül ise(Ör: ```PPAC::add("someModule")```) hedef dosyayı belirlemek için modülün ayar dosyası açılır ve ```mainFile``` değeri hedef dosya olarak belirlenir ve o dosya yüklenir. Eğer modül değil ise dosya çağrıldıysa(Ör: ````PPAC::add("someModule\\Core\\BASE")```) sonuna ```.php``` ifadesi eklenerek hedef dosya belirlenir ve eklenir.

### PPAC'e Modül Yazmak
PPAC'e modül yazmak son derece kolay ve zahmetsizdir. Temelde iki dosyaya sahip olmalıdır: ```package.json``` ve ```index.php```. ```package.json``` dosyası modül hakkındaki bilgileri barındırmaktadır. ```package.json``` dosyasında mutlak bildirilmesi gereken değer(ler):
	- name
		-- Modül İsmi
	- version
		-- Modül Versiyonu
	- description
		-- Modül açıklaması

Örnek bir ```package.json``` dosyası:
```
{
  "name": "someModule",
  "description": "Some module for PPAC",
  "mainFile": "index.php",
  "version": "0.0.1"
}
```

Not: Bu değerler zamanla eklenebilir veya düzeltilebilir.

##### Opsiyonel Özellikler

###### mailFile
```mainFile``` ile modülün ana dosya yolu belirtilir.
Not: ```package.json``` dosyasında belirtilen ```mainFile``` değerine göre gerekli olan ```index.php``` dosyasının ismi değiştirilebilir.

###### firstLoad
```firstLoad``` ile modülün herhangi bir dosyası eklenmeden önce yüklenmesi istenen modül(ler)/dosya(lar) belirtilir. Bu sayede bağımlı olunan modül(ler)/dosya(lar) önceden eklenir.
Ör:
```
{
  ... ,
  "firstLoad" : [
  	"someModule",
  	"otherModule\\Core\\Base",
  ]
}
```
____

PPAC daha yeni başlanmış bir projedir ve ilerletilmeye çalışılmaktadır. PPAC hakkında teknik destek için kalaomer@hotmail.com adresinden iletişimde bulunabilirsiniz.