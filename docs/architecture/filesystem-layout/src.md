# Src

The `src` directory contains the source code specific for the application. Both for the domain and infrastructure layer, among which PHP interfaces and classes. All code in here is covered by unit tests located in the [`test`](./test.md) directory.

You will not find Silex service or controller providers here. They live in the [`app`](./app.md) directory instead, because they shouldn't be unit tested.