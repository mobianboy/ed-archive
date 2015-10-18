CC=c++
CFLAGS=-c -Wall -Waddress -ggdb -std=c++0x
LDFLAGS=
LIBPATH=-L/usr/local/lib
LD_LIBRARY_PATH=-L/usr/local/lib
INCLUDE=-I/usr/local/include -I/home/kryptyk/code/song-ingestion-service/lib/xcoder/include
TARGET=xcoder

all: xcoder

xcoder: transcode_params.o transcoder.o server.o
	$(CC) $(INCLUDE) transcode_params.o transcoder.o server.o $(LIBPATH) -lsox -lpthread -o $(TARGET)

server.o: server.cpp
	$(CC) $(CFLAGS) $(INCLUDE) server.cpp

transcoder.o: transcoder.cpp
	$(CC) $(CFLAGS) $(INCLUDE) transcoder.cpp

transcode_params.o: transcode_params.cpp
	$(CC) $(CFLAGS) $(INCLUDE) transcode_params.cpp

clean:
	rm -rf *o xcoder
