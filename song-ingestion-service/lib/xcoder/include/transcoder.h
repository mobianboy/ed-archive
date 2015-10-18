#ifndef _TRANSCODER_H
#define _TRANSCODER_H

#include <string.h>
#include <cstring>
#include <unistd.h>
#include <stdio.h>
#include <netdb.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>

#include <iostream>
#include <stdio.h>
#include <string.h>
#include <sstream>
#include <assert.h>

#include "transcode_params.h"

extern "C"
{
	#include <sox.h>
}


using namespace std;

class Transcoder
{

private:

	std::string _workingDirectory;

	std::string _callbackAddress;
	unsigned int _callbackPort;

public:

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

	const static unsigned int 	ERR_SOCKET_FAIL			= 190;

	const static unsigned int   STATUS_OK			= 0;

	const static unsigned int   MAX_SAMPLES			= 2048;



	Transcoder();
	~Transcoder();


	// sets up the location for the working directory
	void setWorkingDir(string);
	string getWorkingDir();

	// qualify the artist uploaded file to meet certain
	// minimal quality standards so that it can be used to create media source files
	// will return a bitmask inside of a long integer
	long qualify(long);

	// create a new media file from an existing one
	// source media file
	// output file name
	// target format (int)
	// target bitrate (long)
	// starting point in track to begin in milliseconds (default 0)
	// end point in track end  (default: -1 for none)
	// returns: an error code or a url to the converted file
	int create(string, string, string, long, string, string, int, string, string);

	// dispatches request (json string)
	// parses the JSON file received from the client
	// and creates a thread for each transcoding job
	// in the request
	int dispatch(TranscodeParams*);

	// takes three parameters and embeds in JSON string
	// for sending back to the service that started it
	void respond(unsigned int, std::string, std::string, unsigned int, unsigned int, std::string);

	// set the callback address and port
	// this is what is called when the transcoder finishes a job
	void setCallbackAddressPort(std::string address, unsigned int port);
};

#endif //_TRANSCODER_H
