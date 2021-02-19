# Read a water meter and returns value

Reads analog water meters and provides a web service that returns the read value as decimal.

![CI](https://github.com/nohn/watermeter/workflows/CI/badge.svg)

## Usage

### Running in demo mode

After executing

    docker run -p 127.0.0.1:3000:80 nohn/watermeter:latest

You can access a demo on

    http://127.0.0.1:3000/?debug

respectivly

    http://127.0.0.1:3000/

### Taking the water meter image

I have good results with a Raspberry Pi Zero and a cheap camera. In fact the worse the image quality, the easier it is for the OCR to read the digits in my experience. Night vision cameras does not provide good results, as it's close to impossible to identify the analog gauges with a greyscale image. Instead, I'm using a white led before taking the shots:

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
  wasserzaehler:
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
    resource: "http://watermeter/"
    scan_interval: 60
    unit_of_measurement: 'm³'
```

## How to contribute

You can contribute to this project by:

* Opening an [Issue](https://github.com/nohn/watermeter/issues) if you found a bug or wish to propose a new feature
* Placing a [Pull Request](https://github.com/nohn/watermeter/pulls) with bugfixes, new features etc.

## License

analogmeterreader is released under the [GNU Affero General Public License](LICENSE).