// transcoder.cpp

#include <transcoder.h>
#include <transcode_params.h>

using namespace std;


Transcoder::Transcoder()
{

}

Transcoder::~Transcoder()
{

}

void Transcoder::setWorkingDir(string dir)
{
	_workingDirectory = dir;
}

string Transcoder::getWorkingDir()
{
	return _workingDirectory;
}

long Transcoder::qualify(long trackId)
{

	return 0;
}

void Transcoder::setCallbackAddressPort(std::string address, unsigned int port)
{
	_callbackAddress 	= address;
	_callbackPort 		= port;
}


void Transcoder::respond(unsigned int jobId, std::string filename, std::string format, unsigned int bitrate, unsigned int status, std::string path)
{
	// take the incoming parameters and place in JSON formatted string per spec
	int socket_desc;
	stringstream ss;
	struct sockaddr_in svr;

	_callbackAddress = "127.0.0.1";

	ss << "{\"method\":\"transcodeFinishJob\",\"priority\":10,\"params\":{\"jobId\":" << jobId << ",\"target\":\"" << format << "\",\"bitrate\":" << bitrate << ",\"filename\":\"" << filename << "\",\"status\":" << status << ",\"path\":\"" << path << "\"}}";

	// cout << "ss: " << ss.str() << endl;

	// connect to service that started the job
	socket_desc = socket(AF_INET, SOCK_STREAM, 0);
	if (socket_desc == -1)
	{
		perror("ERROR:  Could not create socket");
		exit(ERR_SOCKET_FAIL);
	}

	// fill with zero
	memset(&svr, 0, sizeof(svr));
	svr.sin_addr.s_addr = inet_addr(_callbackAddress.c_str());
	svr.sin_family 		= AF_INET;
//	svr.sin_port 		= htons(atoi(_callbackPort.c_str()));
	svr.sin_port		= htons(9016);

	// when successfully connected
	int ret = connect(socket_desc,(struct sockaddr *)&svr, sizeof(svr) );
	if (ret < 0)
	{
		cerr << "cannot connect to service at " << _callbackAddress << " port: 9016" << endl;
		return;		// todo hmmm
	}

	// .. send JSON to remote
	memset(&svr, 0, sizeof(svr));

	std::string s = ss.str();
	const char* buf = s.c_str();
	// cout << "PRESEND BUFFER:: " << buf << endl;
	// cout << "BUFSIZE: " << strlen(buf) << endl;
	write(socket_desc, buf, strlen(buf));
	// if received okay, then disconnect
	close(socket_desc);
	// log result

	// end thread
}

// gets the values from params and dispatches an encoder session
int Transcoder::dispatch(TranscodeParams *params)
{
	// extract the values from params
	string filename, outfile, format, startclip, stopclip, inbase, outbase;
	long bitrate;
	int jobId;

	// temp set member vars here
	_callbackAddress 	= "localhost";
	_callbackPort 		= 9016;

	filename 	= params->inFile;
	outfile		= params->outFile;
	format		= params->encodingFormat;
	startclip	= "0";
	stopclip	= "-1";
	bitrate		= params->bitRate;
	jobId		= params->jobId;
	inbase		= params->inFileBaseDir;
	outbase		= params->outFileBaseDir;
/*
	cout << "filename: 		" << filename << endl;
	cout << "format:   		" << format << endl;
	cout << "bitrate:  		" << bitrate << endl;

    int bitRate;
    int bitDepth;
    string effect;
    string outFile;
*/

	// call create
	int status = create(filename, outfile, format, bitrate, startclip, stopclip, jobId, inbase, outbase);

	// send the results back to the server
	// todo -- this status isn't set up right -- should refle
	// the status on the transcoding
    respond(jobId, outfile, format, bitrate, status, outbase);
	return 0;				// signature screw-up -- hack
}



int Transcoder::create(string filename, string outfilename, string targetFormat, long bitrate, string startclip, string endclip, int jobId, string inbase, string outbase)
{

	///////// FIX THIS THING BELOW ///////////
	_workingDirectory 	= "/home/kryptyk/testbed";
	_callbackAddress  	= "";
	_callbackPort 		= 0;

	// inputs and outputs
	sox_format_t *in, *out;
	sox_sample_t samples[MAX_SAMPLES];
	size_t number_read;
	string encType = string(targetFormat);
	sox_encoding_t soxEncType;
	sox_effect_t 	*e;

	sox_effects_chain_t  *chain;

	stringstream ss;
	char* args[10];
	string encodingFormat;
    string inFile;
    string inBaseDirExt;
    string outBaseDirExt;
//    int sampleRate;
//    int bitRate;
//    int bitDepth;
    string effect;
    string outFile;

	// file temporaries
	string inputFile;
	string outputFile;

//	sox_init();
	sox_format_init();

	// faux switch to get the encoding type
	encType = targetFormat;

	if ((encType=="FLAC")||(encType=="flac"))
		soxEncType = SOX_ENCODING_FLAC;
	else if ((encType=="MP3")||(encType=="mp3"))
		soxEncType = SOX_ENCODING_MP3;
	else if ((encType=="OGG")||(encType=="ogg"))
		soxEncType = SOX_ENCODING_VORBIS;
	else if ((encType=="WAV")||(encType=="wav"))
		soxEncType = SOX_ENCODING_ULAW;
	else
		return ERR_UNKNOWN_ENCODING;


	// scale up to a working bitrate value
	bitrate = bitrate * 1000;

	// check for valid bitrate (128k, 320k or lossless)
	if(!((bitrate==128000)||(bitrate==320000)||(bitrate==0)))
		return ERR_INVALID_BITRATE;

	// signal.rate is NOT bitrate, it is sample rate

	inputFile = inbase + "/" + filename;
	outputFile = outbase + "/" + outfilename;

	// open the uploaded media file for transcoding
	in = sox_open_read(inputFile.c_str(), NULL, NULL, NULL);
	// allocate

	out = (sox_format_t *) malloc(sizeof (sox_format_t));
	memcpy(out, in, sizeof(sox_format_t));
	out->encoding.encoding 					= soxEncType;
//	out->encoding.bits_per_sample 	= 16;
	out->signal.rate        				= 44100;
	out->signal.precision						= 16;
	out->signal.length							= SOX_UNSPEC;
	// output file
	//cout << "writing output file: " << outputFile.c_str() << " bitrate: " << bitrate << " :: ";
	out = sox_open_write(outputFile.c_str(), &out->signal, &out->encoding, NULL, NULL, 0);
	//cout << "finished"  << endl;

	// clip/trim if needed	// create a new TranscodeParams instance
	// static int create(std::string&);
	if (!((startclip=="0")&&(endclip=="-1")))
	{
		// equal sign in front of the ending time denotes time from 0
//		ss << "=" << endclip;
//		ss >> endclip;

		chain = sox_create_effects_chain(&in->encoding, &out->encoding);

		// input
		e = sox_create_effect(sox_find_effect("input"));
 		args[0] = (char *)in;

		assert(sox_effect_options(e, 1, args) == SOX_SUCCESS);
  		assert(sox_add_effect(chain, e, &in->signal, &in->signal) == SOX_SUCCESS);
  		free(e);

		// trim
 		e = sox_create_effect(sox_find_effect("trim"));

 		args[0] = (char *)startclip.c_str();  //(char *)&startclip;
		args[1] = (char *)endclip.c_str();

		assert(sox_effect_options(e, 2, args) == SOX_SUCCESS);
  		assert(sox_add_effect(chain, e, &in->signal, &in->signal) == SOX_SUCCESS);
  		free(e);

		out->encoding.encoding		= soxEncType;
		//	out->encoding.bits_per_sample 	= 16;
		out->signal.rate  			= 44100;
		out->signal.precision		= 16;

		// output
  		e = sox_create_effect(sox_find_effect("output"));

		args[0] = (char *)out;
		assert(sox_effect_options(e, 1, args) == SOX_SUCCESS);
  		assert(sox_add_effect(chain, e, &in->signal, &out->signal) == SOX_SUCCESS);
  		free(e);

		sox_flow_effects(chain, NULL, NULL);
		sox_delete_effects_chain(chain);
	}
	// main copy loop
	while ((number_read = sox_read(in, samples, MAX_SAMPLES)) > 0)
	{
		if(sox_write(out, samples, number_read)!= number_read)
			cout << "error:  samples read not equal to samples written" << endl;
	}

	// RELEASE MEMORY
	sox_close(out);
	sox_close(in);

	// delete &inputFile;
	// delete &outputFile;

	sox_format_quit();
//	sox_quit();

	return STATUS_OK;
}


/*

int main(int argc, const char* argv[])
{

	long bitRate;
	string targetFormat;
	string startPoint, stopPoint;
	string inputFile;
    string inBaseDirExt;
    string outBaseDirExt;
    int sampleRate;
    int bitRate;
    int bitDepth;
	cout << "xcoder 0.0 demo" << endl;
	if(argc<2)
	{
		std::cout << "usage: xcoder <input-file> <format> <bitrate> [clipstart] [clipstop]" << std::endl;
		cout << "       <input-file> input file name" << endl;
		cout << "       [format] is one of MP3|FLAC|OGG|WAV|AIFF|PAKG" << endl;
		cout << "       [bitrate] is one of 0|128|192|320"  << endl;
		cout << "       [clipstart] - beginning of clip in xxxx.xx seconds - defaults to 0" << endl;
		cout << "       [clipstop]  - end of clip at xxxx.xx seconds       - defaults to -1 (none)" << endl << endl;
		cout << endl;
		exit(0);
	}
	Transcoder *xcoder = new Transcoder();

	// set the working directory
	xcoder->setWorkingDir((char*)"/home/kryptyk/testbed");

	// get filename (TODO: handle exceptionals)
	inputFile = argv[1];

	// get the desired transcoding format
	if(argc>1)
		targetFormat = argv[2];

	// bitrate
	if(argc>2)
		bitRate = strtol(argv[3],NULL,0);

	cout << "IB: " << targetFormat << endl;

	// clipstart
	if(argc>3)
		startPoint = argv[4];
	else
		startPoint = "0";

	// clipstop
	if(argc>4)
		stopPoint = argv[5];
	else
		stopPoint = "-1";


	// transcode the file
	cout << "status: " << xcoder->create(inputFile, targetFormat, bitRate, startPoint, stopPoint) << endl;

}

*/
