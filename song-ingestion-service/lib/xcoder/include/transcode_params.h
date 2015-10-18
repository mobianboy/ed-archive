#ifndef _TRANSCODE_PARAMS_H
#define _TRANSCODE_PARAMS_H




#include <iostream>
#include <stdio.h>
#include <string.h>
#include <sstream>
#include <assert.h>


using namespace std;

class TranscodeParams {

////////////////////////////////////////////////////////////
/////////////////////////////////// member vars and consts

public:
	// input file original
	// string infile;
	// the directory extensionf
	// string inbasedirext;

	// target
	string encodingFormat;
    string inFile;
    string inFileBaseDir;
    string outFileBaseDir;
    int sampleRate;
    int bitRate;
    int bitDepth;
    string effect;
    string outFile;
	string startClip;
	string endClip;
	int jobId;

	// most likely accepted formats
	const static unsigned int MP3      = 255;

	const static unsigned int FLAC     = 254;
	const static unsigned int WAV      = 253;
	const static unsigned int OGG      = 252;

	// most likely sample rates
	const static unsigned int   SAMPLE_RATE_22K    = 22050;
	const static unsigned int   SAMPLE_RATE_44K    = 44100;			// output target sample rate in hz
	const static unsigned int   SAMPLE_RATE_48K    = 48000;

	// create effects chain for trims/clips

	const static unsigned int   SAMPLE_RATE_96K    = 96000;

	// most likely bit depths
	string _workingDirectory;
	const static unsigned int   BIT_DEPTH_8        = 8;             // won't use this depth - for detection
	const static unsigned int   BIT_DEPTH_16       = 16;            // 16 bit depth
	const static unsigned int   BIT_DEPTH_24       = 24;            // 32 bit depth

	// streaming over phone networks
	const static unsigned long  LOW_BITRATE        = 128000;		// in bits per second
	const static unsigned int   LOW_BITRATE_FORMAT = MP3;
	// streaming over local networks
	const static unsigned long  HIGH_BITRATE        = 320000;		// in bits per second
	const static unsigned int   HIGH_BITRATE_FORMAT = MP3;

	// others
	const static unsigned int   ERR_INVALID_BITRATE 	= 255;
	const static unsigned int   ERR_INVALID_FORMAT  	= 254;
	const static unsigned int   ERR_INVALID_SAMPLERATE	= 253;
	const static unsigned int   ERR_INVALID_BITDEPTH	= 252;
	const static unsigned int   ERR_UNKNOWN_ENCODING	= 251;

	const static unsigned int   ERR_OUTPUT_UNWRITABLE	= 199;
	const static unsigned int   ERR_INPUT_UNREADABLE	= 198;



	const static unsigned int   STATUS_OK			= 0;

	const static unsigned int   MAX_SAMPLES			= 2048;

//////////////////////////////////////////////////////
////////////////////////////////// methods

	TranscodeParams(const char*);

	~TranscodeParams();

};

#endif //_TRANSCODE_PARAMS_H
