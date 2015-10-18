
# SONG INGESTION STATUS:  (5/4/2015) - working, needs to be integrated and tested

### xcoderd JSON transcoding request format (all field example - for illustration purposes)

see wiki for more information 

**version 3 - april 22, 2015**

**request** (sent to transcoder daemon)
<pre><code>
                           {
                             job-id: 14324,
                             infile: "142.wav",
                             infilebasedir: "infiles",
                             outfile: "142.HIGH.MP3",
                             outfilebasedir: "targets"
                             encoding: "MP3",
                             sample-rate: 44100,
                             bitrate:  320,
                             bitdepth: 16,
                             start: "0",
                             end: "-1",
                             effect: "NORMALIZE",
                           }
</code>
</pre>

as of this date, the 'effect' parameter is not functional.  

bitdepth is programmatically set at 16 bits, as sample rate is set to 44.1 khz.  both 'standard' settings in online music streaming.

**response** (sent from transcoder daemon)
<pre><code>
                           {
                             job-id: 14324,
                             file: "142.HIGH.mp3",
                             path: "/home/robin/testbed/targets",
                             status: 0,
                             message: "SUCCESS"
                           }
</code>
</pre>
** status codes** (sent from transcoder daemon)

<pre><code>
                    0   - success
                    1   - problem reading file - file format? missing file?
                    2   - problem writing file - disk space?
                    3   - problem reading file - permissions?
                    4   - problem writing file -- permissions?
 </code>
</pre>                       



value            | definition
---------------- | -------------
infile           | the target (input) file to be transcoded
infilebasedir    | the input file base directory extension where the original file is stored 
outfile          | the output filename - usually it will be the input filename + INDICATOR + type extension
                 | WAV = .wav, LLF = lossless FLAC,  320KMP3 = 320kbps mp3, 128KMP3 = 128kbps mp3 
                 | with eardish, the base filename will also be the trackId in the database
encoding-format  | the target output format (FLAC OR MP3)
sample-rate      | sample-rate (always 44100)
bitrate          | target file bitrate (320|128|0 for FLAC and WAV)
bitdepth         | bit-depth (always 16)
effect         * | post-processing effects to add to target output.  very important.
                 | at the moment, NORMALIZE should be entered here
start            | (optional) the start time of an audio preview clip in xxxx.xx seconds from start   
end              | (optional) the end time of an audio preview clip in xxxx.xx seconds from start
outfilebasedir   | the output target file base directory extension where the target is to be stored
job-id           | transcode_job id (generated by db, and passed from request to response)

 *  = not yet implemented