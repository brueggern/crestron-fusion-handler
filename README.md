# Crestron Fusion Handler

The Crestron Fusion Handler is able to fetch all rooms or appointments using the Crestron Fusion REST API.

## Create Handler
First you need to create a new handler object and set the auth params if enabled on your API.
```
$handler = new CrestronFusionHandler('https://123.45.67.89/fusion/apiservice');
$handler->setAuth('some-token', 'some-user');
```

## Fetch Rooms
Fetch all rooms.
```
$roomsCollection = $handler->getRooms();
```

## Fetch Appointments
Fetch appointments of specific rooms.
```
$rooms = new Collection();
$rooms->addItem(new Room(['id' => '41746b03-1803-4d65-84eb-06815688c780']));
$appointmentsCollection = $handler->getAppointments(new DateTime(), $rooms);
```

## Collections
Instead of arrays, a collection object is always returned. For more information, see `src/Collection.php`
```
$collection = new Collection();
$collection->addItem('Room One', 'room1');
$collection->addItem('Room Two, 'room2');

$name = $collection->getItem('room1');
// $name => Room One

$collection->deleteItem('room2');
// or
$collection->deleteItem(1);

$array = $collection->get();
// Returns an array

$mergedCollection = $collection1->append($collection2)//
// Append all items of a collection to another collection
```

## Development / Tests / Linting
```
composer install
cp tests/.env.testing.example tests/.env.testing

composer run test:online
composer run test:offline
composer run lint
```

## Notes
In future versions it should also be possible to update entities such as rooms, appointments, etc.
