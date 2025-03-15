# Poll_XH

Poll_XH facilitates to place polls on your CMSimple_XH website. You can have
as many polls you like, with as many options you like (single or multiple
choice). The voters are distinguished by cookie and IP address, so cheating
is somewhat unlikely.

- [Requirements](#requirements)
- [Download](#download)
- [Installation](#installation)
- [Settings](#settings)
- [Usage](#usage)
- [Limitations](#limitations)
- [Troubleshooting](#troubleshooting)
- [License](#license)
- [Credits](#credits)

## Requirements

Poll_XH is a plugin for [CMSimple_XH](https://www.cmsimple-xh.org/).
It requires CMSimple_XH ≥ 1.7.0, and PHP ≥ 7.1.0.
Poll_XH also requires the [Plib_XH](https://github.com/cmb69/plib_xh) plugin;
if that is not already installed (see *Settings*→*Info*),
get the [lastest release](https://github.com/cmb69/plib_xh/releases/latest),
and install it.

## Download

The [lastest release](https://github.com/cmb69/poll_xh/releases/latest)
is available for download on Github.

# Installation

The installation is done as with many other CMSimple_XH plugins.

1. Backup the data on your server.
1. Unzip the distribution on your computer.
1. Upload the whole directory `poll/` to your server into
   the `plugins/` directory of CMSimple_XH.
1. Set write permissions for the subdirectories `css/` and `languages/`.
1. Navigate to `Plugins` → `Poll` in the back-end to check
   if all requirements are fulfilled.

## Settings

The configuration of the plugin is done as with many other CMSimple_XH plugins
in the back-end of the Website. Go to `Plugins` → `Poll`.

Localization is done under `Language`. You can translate the character
strings to your own language (if there is no appropriate language file
available), or customize them according to your needs.

The look of Poll_XH can be customized under `Stylesheet`.

## Usage

You can embed a poll on a CMSimple_XH page with the following plugin call:

    {{{poll('name-of-the-poll')}}}

To embed a poll in the template use:

    <?=poll('name-of-the-poll')?>

Instead of `name-of-the-poll` you can use any name as long as it consists
of lowercase latin letters (`a`-`z`), digits (`0`-`9`) and hyphens only.
You can embed as many polls on any single page as you like –
they are working independent of each other as long as they have different names.

If the poll has not ended and the visitor has not voted yet, they will be
presented the voting view and can submit their vote. After they have voted,
they can see the results of the poll.

Currently the poll data files have to be created and edited manually.
Just put a file `name-of-the-poll.csv` into the subfolder
`poll/` of the `content/` folder of CMSimple_XH.
Each voting option has its own line in the file.
Additionally there are two meta options, namely `%%%MAX%%%` and `%%%END%%%`,
which you have to put in separate lines too. Both are optional.

    %%%MAX%%%→3

will make a multiple-choice poll where the user can check 3 options at most,
and with

    %%%END%%%→1335744000

you can specify the end date of the poll as Unix timestamp.
Note that the `→` stands for a `TAB` character.
To calculate the Unix timestamp of a date, you can use an
[online converter](https://www.onlineconversion.com/unix_time.htm).

As an example a `fifa-2018.csv` file is delivered in the
`help/` folder, which is supposed to explain the file format.
The end date of this poll is set to the beginning of the 2018 FIFA World Cup
Russia (which was in June, 14th 2018).
After moving the file to `content/poll/`,
you can embed this poll on a page by writing:

    {{{poll('fifa-2018')}}}

## Limitations

Resetting an already running poll is not possible, because respective
cookies may already been stored on the computer of a voter, so they could
not vote again. As workaround you would have to rename the poll.

32bit versions of PHP cannot handle Unix timestamps larger than `2147483647`
(which is January, 19th 2038). Therefore, with such versions it is not
possible to end polls after that date.

## Troubleshooting

Report bugs and ask for support either on [Github](https://github.com/cmb69/imgzoom_xh/issues)
or in the [CMSimple_XH Forum](https://cmsimpleforum.com/).

## License

Poll_XH is free software: you can redistribute it and/or modify it
under the terms of the GNU General Public License as published
by the Free Software Foundation, either version 3 of the License,
or (at your option) any later version.

Poll_XH is distributed in the hope that it will be useful,
but without any warranty; without even the implied warranty of merchantibility
or fitness for a particular purpose.
See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Poll_XH. If not, see https://www.gnu.org/licenses/.

Copyright © Christoph M. Becker

Czech translation © Josef Němec.

## Credits

The plugin logo is designed by [Jack Cai](https://www.doublejdesign.co.uk/).
Many thanks for publishing this icon under CC BY-ND.

Many thanks to the community at the [CMSimple_XH forum](https://www.cmsimpleforum.com)
for tips, suggestions and testing. Particularly I want to thank
*oldnema*, *svasti*, *bca* and *Tata* for early feedback.

And last but not least many thanks to [Peter Harteg](https://www.harteg.dk/),
the “father” of CMSimple, and all developers of [CMSimple_XH](https://www.cmsimple-xh.org/)
without whom this amazing CMS would not exist.
