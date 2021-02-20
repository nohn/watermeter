# Read a water meter and returns value

Reads analog water meters and provides a web service that returns the read value as decimal.

Turns ![Watermeter](doc/watermeter.jpg) into ```820.5745``` so it can become ![Grafana Screenshot](doc/grafana.png).

![CI](https://github.com/nohn/watermeter/workflows/CI/badge.svg) ![Docker Hub Pulls](https://img.shields.io/docker/pulls/nohn/watermeter?label=docker%20hub%20pulls)

## Demo

After executing

    docker run -p 127.0.0.1:3000:3000 nohn/watermeter:main

You can access a demo on

    http://127.0.0.1:3000/?debug

respectivly

    http://127.0.0.1:3000/

## Usage

### Taking the water meter image

I have good results with a Raspberry Pi Zero and a cheap camera. In fact the worse the image quality, the easier it is for the OCR to read the digits in my experience. To see an example, how bad the quality can be, take a look at the [demo image](src/demo/demo.jpg). Sometimes when the meter is fogged, the quality is even worse, but the results are still accurate. Night vision cameras do not provide good results, as it's close to impossible to identify the analog gauges with a greyscale image. Instead, I'm using a white led before taking the shots:

```python
from gpiozero import LED
from time import sleep
from picamera import PiCamera

led = LED(17) # Choose the correct pin number

camera = PiCamera()
camera.resolution = (2592, 1944)
camera.brightness = 60
led.on()
camera.start_preview()
sleep(5)
camera.capture('/run/shm/wasseruhr_last.jpg')
camera.stop_preview()
led.off()
```

### Preprocessing the meter image

Your mileage may vary, you have to play around a bit. I run 

    convert -contrast -equalize /run/shm/wasseruhr_crop.jpg /run/shm/wasseruhr.jpg

for equalizing the results and improving contrast.

### docker-compose

#### Configuration

Unless you want to run in demo mode, you need to provide a ```config/config.php```. An example is provided in [config/config.php](src/config/config.php)

#### Initial value

Unless you want to run in demo mode, you need to provide the initival value in ```config/lastValue.txt```. An example is provided in [config/lastValue.txt](src/config/lastValue.txt)

#### docker-compose.yaml

```yaml
version: "3.5"
services:
  watermeter:
    image: nohn/watermeter:main
    container_name: watermeter
    restart: always
    volumes:
      - ./watermeter/config:/usr/src/watermeter/src/config
    ports:
      - "3000:3000"
```

#### Integration in Home Assistant

In your ```configuration.yaml``` add

```yaml
sensor:
  - platform: rest
    name: Water
    resource: "http://watermeter:3000/"
    scan_interval: 60
    unit_of_measurement: 'm³'
```

## How to contribute

You can contribute to this project by:

* Opening an [Issue](https://github.com/nohn/watermeter/issues) if you found a bug or wish to propose a new feature
* Placing a [Pull Request](https://github.com/nohn/watermeter/pulls) with bugfixes, new features etc.

## You like this?

Consider a [gift](https://www.amazon.de/hz/wishlist/genericItemsPage/3HYH6NR8ZI0WI).

## License

analogmeterreader is released under the [GNU Affero General Public License](LICENSE).