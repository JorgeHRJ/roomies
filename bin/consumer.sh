#!/bin/bash

sleep 60;

bin/console messenger:consume -vv >&1;
