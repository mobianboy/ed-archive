#!/bin/bash

# TODO When Moved to .config folder change this to be ".ed_profile" or something of the like

# Save Some Paths
ED_PROJECT_PATH="/home/$USER/clientapp"
ED_MODULES_PATH="$ED_PROJECT_PATH/node_modules"
ED_MODULES_BIN="$ED_MODULES_PATH/.bin"

# Add Node Moduels Bin to Path
PATH="$PATH:$ED_MODULES_BIN"

# Alias gulp so that it uses harmony (only works in vagrant not through ssh -c command)
alias gulp="node --harmony --harmony_arrow_functions $ED_MODULES_PATH/gulp/bin/gulp.js"

# PhantomJS Path
#PHANTOMJS_BIN=`command -v phantomjs`
#export  PHANTOMJS_BIN

# Set in start.sh, needs re-export
DISPLAY=:1.5

export PATH ED_PROJECT_PATH ED_MODULES_PATH ED_MODULES_BIN CHROME_BIN DISPLAY

# Helpful Functions
# These must be run inside of vagrant
#  or via `vagrant ssh -c func-name`

# See gulp server procs in vagrant
proc-gulp ()
{
  ps ux | awk '!/awk|bash/ && /npm|node|gulp/ {print}'
}

follow ()
{
  tail -f "$1" 2> >(grep -v truncated >&2)
}

follow-last ()
{
  local logFolder="$ED_PROJECT_PATH/logs/gulp"
  local lastLog="$(ls -t ${logFolder} | head -n1)"
  echo "following ${lastLog}"
  tail -f "${logFolder}/${lastLog}"
}

# This will kill all the procs running the gulp dev server
kill-gulp ()
{
  local ED_GULP_PIDS=`ps axf | awk '!/awk|bash/ && /npm|node|gulp/ {printf "%s ", $1}'`
  echo "gulp pids: $ED_GULP_PIDS"

  if [ -n "$ED_GULP_PIDS" ]; then
    echo "performing kill"
    kill $ED_GULP_PIDS
    proc-gulp
  else
    echo "nothing to kill"
  fi
}

# This will run `npm start` correctly
start-gulp ()
{
  if [ -n "$(proc-gulp)" ]; then
    echo "gulp already running"
  else
    local CWD=`pwd`
    cd "$ED_PROJECT_PATH"

    # source NVM if needed
    command -v npm > /dev/null 2>&1 || source /home/vagrant/.nvm/nvm.sh

    # run in background if no logs
    if [[ $(cat .env | grep -c GULP_LOG_TO_CONSOLE=TRUE) > 0 ]]; then
      npm start
    else
#      npm start &
      nohup npm start > /dev/null 2>&1 &
    fi
    cd $CWD
  fi
  proc-gulp
}

# This just runs the above two commands
restart-gulp ()
{
  kill-gulp ; start-gulp
}

# $1: namespace | < ed | ui | poc >
# $2: elem-name
copy-element ()
{
  # little bit of protection
  if [ "$#" != 2 ]; then
    echo "ERROR: Not the right number of args, must be 2 ie: namespace elem-name"
    return
  fi
  local CWD=`pwd`

  # transform $2 into camel case
  local replaceNext=false
  local varname=""
  for char in `echo "$2" | sed -e "s/\(.\)/\1\n/g"` ; do
    if [ "$replaceNext" == true ]; then
      varname="$varname$(echo $char | tr '[:lower:]' '[:upper:]')"
      replaceNext=false
      continue
    fi
    if [ "$char" == "-" ]; then
      replaceNext=true
    else
      varname="$varname$char"
    fi
  done
  echo "variable name will be: $varname"

  # move dirs and copy ./.new/*
  cd "$ED_PROJECT_PATH/client/components/"
  cp -r "./.new/" "./$1/$2/" && echo "copied sucessfuly"

  # move again and do magic on files
  cd "./$1/$2"
  for file in ./*; do
    sed -i -e "s/new-element/$2/g" "${file}" && echo "rewrote tag style names"
    sed -i -e "s/newElement/${varname}/g" "${file}" && echo "rewrote variable name"
    # TODO only move if filename contains "new-element"
    mv "${file}" "${file/new-element/$2}" && echo "renamed ${file} -> ${file/new-element/$2}"
  done

  # yay done
  echo "created new element in: " && pwd
  cd "$CWD"
}
