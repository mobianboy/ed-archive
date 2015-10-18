#ifndef _media_store_h_
#define _media_store_h_
// MediaStore


class MediaStore
{
private:
public:
	// create a space for the new file and store it
	// pass in trackId (long) and pointer to file information
	virtual int create(long, FILE*);
	// read the media file (non-stream)
	virtual char* read(long);
	// returns an open file pointer to the media file, ready for streaming/reading
	virtual FILE* filePointer(long);
	// update an existing 
}
#endif //_media_store_h_
