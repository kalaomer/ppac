# PPAC ( PHP PACKAGE LOADER )
### TR Anlatım

PPAC'in yapımında tamamen node.js'den esinlenilmiştir. Çalışma prensipleri çok benzerdir bunun için.

PPAC'in asıl amacı paketler halindeki PHP kodlarına kolay ulaşımı sağlamaktır. Paketlere en basit şekilde ulaşmak, onları kullanmak ve güncellemek(ileriki veriyonda) PPAC'in yazılmasındaki asıl amaçtır. Ayrıca Hosting gibi yapılarda da çalışması ve performans olarak gayet tatmin edici olması PPAC'i güçlü yapan özelliklerindendir.

PPAC'in yazılmasında node.js'den esinlenildiği iletilmişti. Node.js'de bulunan node_modules klasörü ve işlevi aynı şekilde php_modules adlı klasörde PPAC'de de uygulanmıştır. Buna ek olarak PPAC'in kurulu olduğu dizinde ortak bir modül havuzu yer almaktadır aynı node.js'deki gibi.

PPAC'in şu an için sadece PACKAGE_INCLUDER'ı vardır. Yani sadece havuzlardan modül veya dosyaları yüklenebilmektedir. İleri ki safhalarda internet üzerinde oluşturulacak ortak bir havuzdan PHP paketleri indirmek mümkün olacaktır.(Projeye gösterilecek destek ile karar verilecek bir aşama)

### PPAC Çalışma Prensibi
PPAC, node.js'deki ```require``` sistemi ile aynı şekilde çalışmaktadır(bkz. http://nodejs.org/api/modules.html#modules_loading_from_node_modules_folders).

Öncelikle yüklü olan paketlerin belirli bir standartta olması gerekmektedir.(https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md). PPAC ilk require işlemi ile eklendiğinde, eklendiği dosyanın klasörünü tavan noktası olarak ele alır. Bu değer havuz ararken sınır olarak kullanılır. Daha sonra PPAC'den Paket veya Class yüklenmesi istediğinde, bu isteğin yollandığı dosya taban olarak kabul edilir. Sonrasında eklenmek istenen öğenin paket adresi eğer bilinmiyor ise taban ile tavan arasındaki havuzlara bakılır. Eğer orada bulunamaz ise sonrasında ortak havuza bakılır(Ortak havuza önce bakılması önceliği config.php'den ayarlanabilmektedir).

### PPAC Kurulumu
PPAC kurulumu gayet basittir. 'dev' klasörü içindeki dosyaları istediğiniz bir yere kopyalamanız yeterlidir.

### PPAC ile Paket eklemek
Önce PPAC'i require ile eklemeniz gerekir.
```
require "../ppac/dev/includer.php";
```
Daha sonrasında PPAC ile yüklü olan bir paketi çağırmak için
```PPAC::add( $paketYolu )```
kullanılır.

Örneğin PPAC içinde hazırda olarak verilen EasyArray'ı eklemek için ```PPAC::add("EasyArray")``` denmesi yeterlidir. PPAC otomatik olarak prejedeki ve ortak alandaki havuzu tarayarak EasyArray paketini arar ve bulup ekler.

### PPAC ile Otomatik Paket Eklemek