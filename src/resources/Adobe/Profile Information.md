*10/15/08*

# Adobe® ICC Profiles

Build quality color workflows with Adobe ICC profiles.

Adobe wants to enable consistent, high quality color workflows. For color workflows to succeed, color information must be shared by many people, from original creator to final publisher. For this reason we are supplying our ICC profiles as a free download for graphics professionals to use across their workflows.

Enclosed in our compressed archive are:

**8 RGB profiles**

- Adobe RGB (1998)
- Apple RGB
- ColorMatch RGB
- SMPTE-C
- PAL/SECAM
- HDTV (Rec. 709)
- SDTV NTSC
- SDTV PAL

**14 CMYK profiles**

- US Web Coated (SWOP) v2
- US Web Uncoated v2
- Coated GRACoL 2006 (ISO 12647-2:2004)
- Web Coated SWOP Grade 3 Paper
- Web Coated SWOP Grade 5 Paper
- Coated FOGRA27 (ISO 12647-2:2004)
- Web Coated FOGRA28 (ISO 12647-2:2004)
- Uncoated FOGRA29 (ISO 12647-2:2004)
- Coated FOGRA39 (ISO 12647-2:2004)
- Japan Web Coated (Ad)
- Japan Color 2001 Coated
- Japan Color 2001 Uncoated
- Japan Color 2002 Newspaper
- Japan Color 2003 Web Coated

**NOTE**: Users who have already installed recent versions of Adobe® Suites or certain Adobe® applications may not need to install these profiles. Many of these profiles are already included and installed when you install and update certain Adobe® applications.

For more information on ICC profiles, visit the ICC (International Color Consortium) web site, [http://www.color.org/](http://www.color.org/).

For more information on ICC Color Management, see information on the Adobe web site, [http://www.adobe.com/](http://www.adobe.com/).


## Profile Installation

### To install the Adobe ICC Profiles on Mac OS X:

You should install profiles in one of two locations.

Copy all of the ICC profiles to the /Users/<your login user name>/Library/ColorSync/Profiles folder. Profiles installed in this location will be available only to the user who installed the profiles.

or

Copy all of the ICC profiles to the /Library/ColorSync/Profiles folder. Profiles installed in this location require the user to be an administrator of the system. Profiles installed in this location will be available to all users.

### To install the Adobe ICC Profiles on Windows 2000/XP & Vista:

Select the profiles in the “RGB Profiles” folder. While the profiles are selected, click and hold the right mouse button and choose the “Install Profile” menu item. Use this same procedure for the profiles in the “CMYK Profiles” folder. Profiles will be copied to the correct filesystem location and the Windows Registry will be updated. If the “Uninstall Profile” menu item appears you already have an Adobe ICC profile of the same name installed.

You may need to relaunch certain applications in order to access these profiles.


## Reference Information for Adobe ICC Color Profiles


### RGB Profiles


#### Adobe RGB (1998)

A color space with a larger gamut than sRGB IEC61966-2.1 but smaller than ProPhoto RGB. Good choice for print publication. Almost all colors available using standard offset press printing conditions can be encoded using this profile. Decodes color values to viewing room colorimetry. The specification for Adobe RGB (1998) can be found here: “[http://www.adobe.com/digitalimag/adobergb.html](http://www.adobe.com/digitalimag/adobergb.html)”. This is an output-referred, version 2 ICC profile.

#### Apple RGB

Describes the color characteristics of the legacy Apple Trinitron monitor, commonly used for press workflows before the widespread adoption of color management. This is an output-referred, version 2 ICC profile.

#### ColorMatch RGB

Describes the color characteristics of the Radius Pressview monitor, commonly used for press workflows before the widespread adoption of color management. This is an output-referred, version 2 ICC profile.

#### SMPTE-C

Color space based on color standards in the US television industry. Supersedes color definitions in the NTSC 1953 ICC profile. The specification for SMPTE-C can be found here: “[http://www.smpte.org/smpte_store/standards/](http://www.smpte.org/smpte_store/standards/)”. This is an output-referred, version 2 ICC profile. SMPTE-C is the output-referred equivalent of the SDTV NTSC profile mentioned below.

#### PAL/SECAM

Color space based on color standards in the European television industry. The specification for PAL/SECAM can be found here: “[http://www.ebu.ch/CMSimages/en/tec_doc_t3213_tcm6-10508.pdf](http://www.ebu.ch/CMSimages/en/tec_doc_t3213_tcm6-10508.pdf)”. This is an outputreferred, version 2 ICC profile. PAL/SECAM is the outputreferred equivalent of the SDTV PAL profile mentioned below.

#### HDTV (Rec. 709)

Color space for high-definition video cameras. The specification for the ITU-R Recommendation 709 can be downloaded here: “[http://www.itu.int/rec/R-RECBT.709-5-200204-I/en](http://www.itu.int/rec/R-RECBT.709-5-200204-I/en)”. This is a camera-referred, version 4 ICC profile.

#### SDTV NTSC

Color space for NTSC video cameras. Uses the same tone response curve as that defined for HDTV (Rec. 709) specified in ITU-R Recommendation 709. This is a camera-referred, version 4 ICC profile.

#### SDTV PAL

Color space for PAL video cameras. Uses the same tone response curve as that defined for HDTV (Rec. 709) specified in ITU-R Recommendation 709. This is a camera-referred, version 4 ICC profile.


### CMYK Profiles


*NOTE: Gray balance and black start values are relative to paper white.*

#### U.S. Web Coated (SWOP) v2

__Characterization used:__ CGATS TR 001  
__URL for characterization:__ [http://www.color.org/drsection1.html](http://www.color.org/drsection1.html)  
__K generation:__ light GCR  
__Black start:__ L*= 66, C = 40%  
__Max. K:__ 90%  
__Max. total ink:__ 300%  
__Paper White:__ L = 88.73, a = -0.27, b = 3.66  
__Gray Balance at 25% Cyan:__ C=25, M=20, Y=20  
__Gray Balance at 50% Cyan:__ C=50, M=41, Y=41  
__Gray Balance at 75% Cyan:__ C=75, M=68, Y=67

*For additional information on the output conditions used to produce the CGATS TR 001 characterization, see [http://www.npes.org/](http://www.npes.org/) or [http://www.printtools.org/](http://www.printtools.org/)*

#### U.S. Web Uncoated v2

__Characterization used:__ GRACoL test sheet  
__URL for characterization:__ none  
__K generation:__ light GCR  
__Black start:__ L* = 67, C = 38%  
__Max. K:__ 95%  
__Max. total Ink:__ 260%  
__Paper White:__ L = 91.25, a = 0.48, b = -2.3  
__Gray Balance at 25% Cyan:__ C = 25, M = 18, Y = 17  
__Gray Balance at 50% Cyan:__ C = 50, M = 39, Y = 38  
__Gray Balance at 75% Cyan:__ C = 75, M = 62, Y = 63

#### Coated GRACoL 2006 (ISO 12647-2:2004)

__Characterization used:__ CGATS TR 006  
__URL for characterization:__ [http://www.color.org/drsection1.html](http://www.color.org/drsection1.html)  
__K generation:__ medium GCR  
__Black start:__ L* = 79, C = 26%  
__Max. K:__ 98%  
__Max. total Ink:__ 340%  
__Paper White:__ L = 95, a = -0.02, b = -1.96  
__Gray Balance at 25% Cyan:__ C = 25, M = 18, Y = 18  
__Gray Balance at 50% Cyan:__ C = 50, M = 40, Y = 40  
__Gray Balance at 75% Cyan:__ C = 75, M = 66, Y = 65

*For additional information on GRACoL-recommended print output parameters, see [http://www.gracol.org/](http://www.gracol.org/)*

#### Web Coated SWOP Grade 3 Paper

__Characterization used:__ CGATS TR 003  
__URL for characterization:__ [http://www.color.org/drsection1.html](http://www.color.org/drsection1.html)  
__K generation:__ medium GCR  
__Black start:__ L* = 78, C = 26%  
__Max. K:__ 98%  
__Max. total Ink:__ 310%  
__Paper White:__ L = 92.5, a = 0, b = 0  
__Gray Balance at 25% Cyan:__ C = 25, M = 19, Y = 20  
__Gray Balance at 50% Cyan:__ C = 50, M = 41, Y = 41  
__Gray Balance at 75% Cyan:__ C = 75, M = 66, Y = 65

*For additional information on SWOP-recommended print output parameters, see [http://www.swop.org/](http://www.swop.org/)*

#### Web Coated SWOP Grade 5 Paper

__Characterization used:__ CGATS TR 005  
__URL for characterization:__ [http://www.color.org/drsection1.html](http://www.color.org/drsection1.html)  
__K generation:__ medium GCR  
__Black start:__ L* = 80, C = 24%  
__Max. K:__ 98%  
__Max. total Ink:__ 300%  
__Paper White:__ L = 90.06, a = -0.01, b = 4.14  
__Gray Balance at 25% Cyan:__ C = 25, M = 19, Y = 21  
__Gray Balance at 50% Cyan:__ C = 50, M = 41, Y = 43  
__Gray Balance at 75% Cyan:__ C = 75, M = 67, Y = 68

*For additional information on SWOP-recommended print output parameters, see [http://www.swop.org/](http://www.swop.org/)*

#### Coated FOGRA27 (ISO 12647-2:2004)

__Characterization used:__ FOGRA27  
__URL for characterization:__ [http://www.color.org/drsection1.html](http://www.color.org/drsection1.html)  
__K generation:__ light GCR  
__Black start:__ L* = 65, C = 45%  
__Max. K:__ 100%  
__Max. total Ink:__ 350%  
__Paper White:__ L = 95.97, a = 0.50, b = -3.30  
__Gray Balance at 25% Cyan:__ C = 25, M = 19, Y = 18  
__Gray Balance at 50% Cyan:__ C = 50, M = 40, Y = 37  
__Gray Balance at 75% Cyan:__ C = 75, M = 67, Y = 61

*For additional information on the output conditions used to produce the FOGRA27 characterization, see [http://www.fogra.org/](http://www.fogra.org/)*

#### Web Coated FOGRA28 (ISO 12647-2:2004)

__Characterization used:__ FOGRA28  
__URL for characterization:__ [http://www.color.org/drsection1.html](http://www.color.org/drsection1.html)  
__K generation:__ light GCR  
__Black start:__ L* = 66, C = 41%  
__Max. K:__ 98%  
__Max. total Ink:__ 300%  
__Paper White:__ L = 92.37, a = -0.70, b = 1.52  
__Gray Balance at 25% Cyan:__ C = 25, M = 19, Y = 19  
__Gray Balance at 50% Cyan:__ C = 50, M = 40, Y = 38  
__Gray Balance at 75% Cyan:__ C = 75, M = 66, Y = 59

*For additional information on the output conditions used to produce the FOGRA28 characterization, see [http://www.fogra.org/](http://www.fogra.org/)*

#### Uncoated FOGRA29 (ISO 12647-2:2004)

__Characterization used:__ FOGRA29  
__URL for characterization:__ [http://www.color.org/drsection1.html](http://www.color.org/drsection1.html)  
__K generation:__ light GCR  
__Black start:__ L* = 66, C = 40%  
__Max. K:__ 86%  
__Max. total Ink:__ 300%  
__Paper White:__ L = 95.71, a = 0.61, b = -2.32  
__Gray Balance at 25% Cyan:__ C = 25, M = 19, Y = 18  
__Gray Balance at 50% Cyan:__ C = 50, M = 41, Y = 39  
__Gray Balance at 75% Cyan:__ C = 75, M = 70, Y = 65

*For additional information on the output conditions used to produce the FOGRA29 characterization, see [http://www.fogra.org/](http://www.fogra.org/)*

#### Coated FOGRA39 (ISO 12647-2:2004)

__Characterization used:__ FOGRA39  
__URL for characterization:__ [http://www.color.org/drsection1.html](http://www.color.org/drsection1.html)  
__K generation:__ medium GCR  
__Black start:__ L* = 86, C = 18%  
__Max. K:__ 330%  
__Max. total Ink:__ 98%  
__Paper White:__ L = 95, a = 0, b = -2  
__Gray Balance at 25% Cyan:__ C = 25, M = 18, Y = 19  
__Gray Balance at 50% Cyan:__ C = 50, M = 40, Y = 40  
__Gray Balance at 75% Cyan:__ C = 75, M = 65, Y = 64

*For additional information on the output conditions used to produce the FOGRA39 characterization, see [http://www.fogra.org/](http://www.fogra.org/)*

#### Japan Color 2001 Coated

__Characterization used:__ JC200103  
__URL for characterization:__ [http://www.color.org/drsection1.html](http://www.color.org/drsection1.html)  
__Black start:__ L* = 49, C = 62%  
__Max. K:__ 80%  
__Max. total Ink:__ 350%  
__Paper White:__ L = 91.05, a = 0.28, b = -1.46  
__Gray Balance at 25% Cyan:__ C = 25, M = 19, Y = 18  
__Gray Balance at 50% Cyan:__ C = 50, M = 41, Y = 39  
__Gray Balance at 75% Cyan:__ C = 75, M = 69, Y = 65

*For additional information on the output conditions used to produce the JC200103 characterization, see [http://www.jpma-net.or.jp/](http://www.jpma-net.or.jp/)*

#### Japan Color 2001 Uncoated

__Characterization used:__ JC200104  
__URL for characterization:__ [http://www.color.org/drsection1.html](http://www.color.org/drsection1.html)  
__Black start:__ L* = 59, C = 42%  
__Max. K:__ 80%  
__Max. total Ink:__ 310%  
__Paper White:__ L = 92.32, a = 0.13, b = -0.32  
__Gray Balance at 25% Cyan:__ C = 25, M = 20, Y = 20  
__Gray Balance at 50% Cyan:__ C = 50, M = 40, Y = 45  
__Gray Balance at 75% Cyan:__ C = 75, M = 72, Y = 86

*For additional information on the output conditions used to produce the JC200104 characterization, see [http://www.jpma-net.or.jp/](http://www.jpma-net.or.jp/)*

#### Japan Web Coated (Ad)

__Characterization used:__ JMPA provided print condition for DDCP (digital proof)  
__URL for characterization:__ [http://www.j-magazine.or.jp/](http://www.j-magazine.or.jp/)

*For information on the output conditions used to produce the print characterization, see [http://www.j-magazine.or.jp/](http://www.j-magazine.or.jp/)*

#### Japan Color 2002 Newspaper

__Characterization used:__ JCN2002  
__URL for characterization:__ [http://www.color.org/drsection1.html](http://www.color.org/drsection1.html)  
__Black start:__ L* = 65, C = 39%  
__Max. K:__ 95%  
__Max. total Ink:__ 240%  
__Paper White:__ L = 80.26, a = -0.24, b = 4.67  
__Gray Balance at 25% Cyan:__ C = 25, M = 20, Y = 22  
__Gray Balance at 50% Cyan:__ C = 50, M = 42, Y = 44  
__Gray Balance at 75% Cyan:__ C = 75, M = 73, Y = 72

*For additional information on the output conditions used to produce the JCN2002 characterization, see [http://www.jpma-net.or.jp/](http://www.jpma-net.or.jp/)*

#### Japan Color 2003 Web Coated

__Characterization used:__ JCW2003  
__URL for characterization:__ [http://www.color.org/drsection1.html](http://www.color.org/drsection1.html)  
__Black start:__ L* = 52, C = 57%  
__Max. K:__ 80%  
__Max. total Ink:__ 320%  
__Paper White:__ L = 92.3, a = -0.51, b = 0.03  
__Gray Balance at 25% Cyan:__ C = 25, M = 18, Y = 19  
__Gray Balance at 50% Cyan:__ C = 50, M = 38, Y = 38  
__Gray Balance at 75% Cyan:__ C = 75, M = 67, Y = 65

*For additional information on the output conditions used to produce the JCW2003 characterization, see [http://www.jpma-net.or.jp/](http://www.jpma-net.or.jp/)*
