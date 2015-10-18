// transcode_params.cpp

#include <transcode_params.h>

#include "rapidjson/document.h"
#include "rapidjson/writer.h"
#include "rapidjson/stringbuffer.h"
// #include <iostream>

using namespace std;
using namespace rapidjson;


TranscodeParams::TranscodeParams(const char* json)
{
        rapidjson::Document doc;
        doc.Parse(json);
    //    assert(doc.IsObject());
        jobId           = doc["job-id"].GetInt();
        encodingFormat  = doc["encoding"].GetString();
        inFile          = doc["infile"].GetString();
        inFileBaseDir    = doc["infilebasedir"].GetString();
        outFileBaseDir   = doc["outfilebasedir"].GetString();
    //    sampleRate      = doc["sample-rate"].GetInt();
        bitRate         = doc["bitrate"].GetInt();
    //    bitDepth        = doc["bitdepth"].GetInt();
    //    effect          = doc["effect"].GetString();
        outFile         = doc["outfile"].GetString();
        startClip       = doc["start"].GetString();
        endClip         = doc["end"].GetString();

}

TranscodeParams::~TranscodeParams()
{

}
