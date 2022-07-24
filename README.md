# Cabinet115

Неофициальное API 115.бел

## Composer

```
composer require opendataworld/cabinet115
```

## Примеры

### Получение заявок и комментариев

```php
$cabinet115 = new OpenDataWorld\Cabinet115($login, $pass, $token);

$orders = $cabinet115->getOrders();

foreach ($orders as $order) {
    $comments = $cabinet115->getOrderComments($order['id_request']);
}
```

### Отправка заявки

```php
$cabinet115 = new OpenDataWorld\Cabinet115bel($login, $pass);
$cabinet115->start();
$cabinet115->login();
$cabinet115->createReport($order_description, $images_array, $latitude, $lngitude);

$data_reports = $cabinet115->getReportsMe();

if ( ! empty($data_reports['reports'][0])) {
    $order115_id = $data_reports['reports'][0]['report_id'];
}
```

### Другие

- Получение списка заявок на указанном участке карты
- Получение картинок
- Получение мест