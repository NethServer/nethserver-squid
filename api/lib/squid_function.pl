#!/usr/bin/perl

#
# Copyright (C) 2019 Nethesis S.r.l.
# http://www.nethesis.it - nethserver@nethesis.it
#
# This script is part of NethServer.
#
# NethServer is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License,
# or any later version.
#
# NethServer is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with NethServer.  If not, see COPYING.
#

use strict;
use warnings;
use JSON;
use esmith::ConfigDB;

require '/usr/libexec/nethserver/api/lib/helper_functions.pl';

sub delete_profile
{
    my $input = shift;
    my $db = esmith::ConfigDB->open('contentfilter');
    my $profile = $db->get($input->{'name'});

    my $f = $profile->prop('Filter');
    my $can_delete_filter = 1;
    # check if the same filter is used anywhere else
    foreach my $p ($db->get_all_by_prop('type' => 'profile')) {
        next if ($p->key eq $input->{'name'}); # skip current profile
        if ($p->prop('Filter') eq $f || $p->prop('Filter') eq 'default') { # preserve default
            $can_delete_filter = 0;
        }
    }
    $f =~ s/(.*);//;
    my $filter = $db->get($f);

    my $t = $profile->prop('Time') || '';
    if ($t) {
        foreach my $time_key (split(",",$t)) {
            my $can_delete_time = 1; 
            # check if the same time is not used anywhere else
            foreach my $p ($db->get_all_by_prop('type' => 'profile')) {
                next if ($p->key eq $input->{'name'}); # skip current profile
                next if (!$p->prop('Time'));
                foreach (split(",",$p->prop('Time'))) {
                    if ($_ eq $time_key) {
                        $can_delete_time = 0;
                    }
                }
            }
            if ($can_delete_time) {
                $time_key =~ s/(.*);//;
                my $time = $db->get($time_key);
                $time->delete();
            }
        }
    }

    $profile->delete();

    if ($can_delete_filter) {
        $filter->delete();
    }
}

sub create_profile
{
    my $input = shift;
    my $db = esmith::ConfigDB->open('contentfilter');

    my $obj = {type => 'profile', 'Removable' => 'yes', Description => $input->{'Description'} || ''};

    my $fobj = {type => 'filter', Description =>  $input->{'Filter'}{'Description'} || 'Auto-created for '.$input->{'name'}};
    foreach (qw(BlockIpAccess BlockFileTypes BlackList BlockAll WhiteList)) {
        $fobj->{$_} = $input->{'Filter'}{$_};
    }
    $fobj->{'Categories'} =  join(",",@{$input->{'Filter'}{'Categories'}});
    $db->new_record($input->{'name'}."filter", $fobj);
    $obj->{'Filter'} ='filter;'.$input->{'name'}.'filter';

    if ($input->{'Src'}) {
        $obj->{'Src'} = $input->{'Src'}{'type'}.';'.$input->{'Src'}{'name'};
    } else {
        $obj->{'Src'} = '';
    }

    if ($input->{'Time'}) {
        my @times;
        my $k = 0;
        foreach my $t (@{$input->{'Time'}}) {
            my $name = $input->{'name'}."time$k";
            my $tobj = {type => 'time', Description => $t->{'Description'} || 'Auto-created for '.$input->{'name'}};
            foreach (qw(StartTime EndTime)) {
                $tobj->{$_} = $t->{$_};
            }
            $tobj->{'Days'} = join(",",@{$t->{'Days'}});
            $db->new_record($name, $tobj);
            push(@times, 'time;'.$name);
            $k++;
        }
        $obj->{'Time'} = join(",", @times);

    } else {
        $obj->{'Time'} = '';
    }

    if ($input->{'FilterElse'}) {
        # extract filter name from profile record
        my $filter = $db->get_prop($input->{'FilterElse'}, 'Filter') || '';
        if ($filter) {
            $obj->{'FilterElse'} = $filter;
        }
    } else {
        $obj->{'FilterElse'} = '';
    }

    $db->new_record($input->{'name'}, $obj);
}


1;
