# Maintainers Instructions

This document contains some instructions for the repository maintainers.


## Publishing a new Imagine release

Follow these instructions when publishing a new Imagine release:

1. Be sure that TravisCI jobs succesfully completed in the `develop` branch
2. Update the `CHANGELOG.md` file:
    - replace the `### NEXT (YYYY-MM-DD)` line with the release number and date  
3. Set the new version in the `Imagine\Image\ImagineInterface::VERSION` constant:
    - it's defined in the file `src/Image/ImagineInterface.php`
4. Commit these changes and push to the `develop` branch
5. Wait until TravisCI jobs complete succesfully
    - one of those jobs should automatically add a new commit to the `develop` branch, updating the API docs
6. Create a new release on GitHub
    - Go to https://github.com/avalanche123/Imagine/releases/new and enter this data:
        - Tag version: the new version (just the numbers/dots - for example `1.2.3`)
        - Release title: the new version (with a leating `v` - for example `v1.2.3`)
        - Release description: copy the relevant section from the `CHANGELOG.md` file
    - the new release should appear on https://imagine.readthedocs.io in a short time
7. Fast forward the `master` branch to this new tag (for example `1.2.3`) 
8. Wait until TravisCI jobs complete
9. Update the `CHANGELOG.md` file:
    - add a new `### NEXT (YYYY-MM-DD)` line
10. Set the new development version in the `Imagine\Image\ImagineInterface::VERSION` constant:
    - it's defined in the file `src/Image/ImagineInterface.php`
11. Drink a beer to celebrate the new version
