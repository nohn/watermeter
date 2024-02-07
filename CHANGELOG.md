# 2024-02-08 v1.2.22
- update PHP to 8.3.2

# 2024-02-08 v1.2.21
- don't fail configuration if OCR fails (#113)	

# 2023-02-19 v1.2.20
- update PHP to 8.2.3

# 2022-12-31 v1.2.19
- don't fail on empty digits
- allow to remove all digits/gauges

# 2022-12-27 v1.2.18

- allow to invert digits to improve OCR
- allow to remove all digits/gauges
- update PHP to 8.2.0

# 2022-11-02 v1.2.17

- improve howto: integration with home asssistant energy dashboard
- update PHP to 8.1.12
- ensure PHPUnit 9.5.14 compatibility

# 2022-03-01 v1.2.16

- improve howto - be more specific about docker-compose
- fix #16 - add support for decimal digits
- add support for image decolorization

# 2022-02-20 v1.2.15

- improve testability
- fix #38 - validate detected digits

# 2022-02-19 v1.2.14

- additional test cases

# 2022-02-19 v1.2.13

- fix issue with threshold handling
- fix typos

# 2022-02-19 v1.2.12

- update PHP to 8.1.3
- improve howto
- improve test coverage
- fix #33 - add support for offsets

# 2022-02-18 v1.2.11

- fix #55 - write processed images to file system so they can be served easier

# 2022-02-18 v1.2.10

- fix #49 - image postprocessing in watermeter

# 2022-02-18 v1.2.9

- fix some paths

# 2022-02-18 v1.2.8

- fix #15 - Move logic to testable classes and add tests
- update PHP to 8.1.2

# 2021-10-02 v1.2.7

- update PHP to 8.0.11
 
# 2021-09-21 v1.2.6

- fix #36 - add a digit results in error
- allow to disable image postprocessing

# 2022-09-15 v1.2.5

- fix #35 - add howto

# 2022-09-09 v1.2.4

- update PHP to 8.0.10

# 2022-08-24 v1.2.3

- fix #31 - provide docker images for linux/amd64, linux/arm64, linux/arm/v7, linux/arm/v6
- fix #9 - Migrate CI to Github Docker Action v2 
- run builds with PHP 8
- update PHP to 8.0.9

# 2022-08-24 v1.2.2

- fix some PHP warnings and typos

# 2022-06-19 v1.2.1

- improve logging

# 2021-02-28 v1.2.0

- Upgrade nohn/analogmeterreader to ^1.2 improving analog gauge recognition accuracy

# 2021-02-23 v1.0.0

- Fix #5 - JSON output including more details
- Fix #6 - Allow to enable/disable logging
- Simplify setup

# 2021-02-22 v0.2.0

- Fix #3 - Allow to add or remove digits in configuration GUI
- Fix #4 - Allow to add or remove gauges in configuration GUI
- Fix #8 - Docker Hub tag for Git branch "main" should be "latest" (so it's nohn/watermeter:latest from now on, nohn/watermeter:main will not work anymore)

# 2021-02-22 v0.1.0

- Fix #1 - Add configuration GUI.

# 2021-02-19 v0.0.1

- Initial public release
