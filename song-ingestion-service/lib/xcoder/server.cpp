/*
    C socket serverß, handles multiple clients using threads
    Compile
    gcc server.c -lpthread -o server
*/

// xcoderd - transcoding daemon 0.8 -- eardish - may 2015


#include <stdio.h>
#include <string.h>    //strlen
#include <stdlib.h>    //strlen
#include <sys/socket.h>
#include <arpa/inet.h> //inet_addr
#include <unistd.h>    //write
#include <pthread.h> //for threading , link with lpthread
#include <sstream>
#include <iostream>

// #include <thread>


#include "transcoder.h"
#include "transcode_params.h"

using namespace std;



const static unsigned int SUCCESS       = 0;
const static unsigned int PTHREAD_FAIL  = 1;
const static unsigned int BIND_FAIL     = 2;
const static unsigned int ACCEPT_FAIL   = 3;
const static unsigned int RECV_FAIL     = 4;
const static unsigned int CLIENT_FAIL   = 5;
const static unsigned int JSON_FAIL     = 6;
const static unsigned int SOCKET_FAIL   = 7;

// set backlog to number of simultaneous connections to queue up before refusing
const static unsigned int BACKLOG       = 5;


//the thread function
void *connection_handler(void *);


int main(int argc , char *argv[])
{
    int socket_desc , client_sock , c;
    struct sockaddr_in server , client;

    //Create socket
    socket_desc = socket(AF_INET , SOCK_STREAM , 0);
    if (socket_desc == -1)
    {
        perror("ERROR:  Could not create socket");
        exit(SOCKET_FAIL);
    }

    //Prepare the sockaddr_in structure
    server.sin_family       = AF_INET;
    server.sin_addr.s_addr  = INADDR_ANY;
    server.sin_port         = htons(9005);

    //Bind
    if( bind(socket_desc,(struct sockaddr *)&server , sizeof(server)) < 0)
    {
        //print the error message
        perror("ERROR:  Bind failure");
        exit(BIND_FAIL);
    }

    //Listen - set the BACKLOG at the top
    listen(socket_desc , BACKLOG);
    c = sizeof(struct sockaddr_in);
    pthread_t thread_id;
    cout << "xcoderd 0.8 - listening on port 9005" << endl;
    
    // wait for incoming connections
    while( (client_sock = accept(socket_desc, (struct sockaddr *)&client, (socklen_t*)&c)) )
    {
        if( pthread_create( &thread_id , NULL ,  connection_handler , (void*) &client_sock) < 0)
        {
            perror("could not create thread");
            // return PTHREAD_FAIL;
        }
        pthread_detach(thread_id);
    }
    if (client_sock < 0)
    {
        perror("accept failed");
        exit(ACCEPT_FAIL);
    }
    return SUCCESS;
}

/*
 * This will handle connection for each client
 * */

void *connection_handler(void *socket_desc)
{
    //Get the socket descriptor
    int sock = *(int*)socket_desc;
    int read_size;
    char msg[1];
    stringstream ssBuf;

    TranscodeParams *params;
    Transcoder *xcoder;

    // read on socket
    int count = 0;
    while((read_size = (recv(sock , msg , 1 , 0)) > 0))
    {
        count++;
        ssBuf << *msg;          /////////////////////
        if(*msg=='}')           // <<<<< FIX FIX FIX
        {
            params = new TranscodeParams(ssBuf.str().c_str());
            xcoder = new Transcoder();
            xcoder->dispatch(params);
            break;
        }
    }
    delete params;
    delete xcoder;


    if(read_size == 0)
    {
        fflush(stdout);
        return (void*)CLIENT_FAIL;
    }
    if(read_size == -1)
    {
        perror("recv() failed");
        return (void*)RECV_FAIL;
    }
    return (void*)SUCCESS;

}
