# PPAC ( PHP PACKAGE LOADER )
### TR Anlatım

PPAC'in çalışma prensibinde tamamen node.js'den esinlenilmiştir. Çalışma prensipleri de çok benzerdir bunun için.

PPAC ortak bir havuzda PHP paketlerini tutmaktadır. Buna ek olarak projeler kendilerine has havuzlarda kullanabilirler.

PPAC'in şu an için sadece PACKAGE_INCLUDER'ı vardır. Yani sadece havuzlardan modül veya dosyaları yüklenebilmektedir. İleri ki safhalarda internet üzerinde oluşturulacak ortak bir havuzdan PHP paketleri indirmek mümkün olacaktır.

PPAC'in asıl avantajı, hostlarda da kullanılabilmesidir. Sadece PPAC dosyalarının host'a kopyalanıp gerekli senkronizasyonların yapılması yeterlidir.

### PPAC kurulumu
PPAC kurulumu gayet basittir. 'dev' klasörü içindeki dosyaları istediğiniz bir yere kopyalamanız yeterlidir.

### PPAC ile Paket eklemek
Önce PPAC'i require ile eklemeniz gerekir.
```
require "../dev/includer.php";
```
Daha sonrasında PPAC ile yüklü olan bir paketi çağırmak için
```
PPAC::add( $pakeAdi )
```
kullanılır. 
