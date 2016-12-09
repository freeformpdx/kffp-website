# Freeform Portland Wordpress Site

### Set Up Local Development Environment:

Clone the repository
```
$ git clone git@github.com:freeformpdx/kffp-website.git
```

Install [Virtual Box](https://www.virtualbox.org/wiki/Downloads?replytocom=98578)

Install [Vagrant](https://www.vagrantup.com/downloads.html)


(If you're on a Mac, be sure to have [Homebrew](http://brew.sh/) installed)


Install `Ansible` using `Homebrew`:
```
$ brew install ansible
```

(For other platforms, visit the [Ansible](https://www.ansible.com/) site)

Prepare for Database import:
```
Obtain the backup.sql file from a team member and place a copy in the project root.
```

cd into the project root:
```
$ cd kffp-website
```

Spin up the VM:
```
$ vagrant up
```

The initial Vagrant build can take several minutes.

First, Vagrant will build the VM, then run Ansible, which provisions the VM, installing all the necessary software to run the application as well as importing the database from the backup.

Some features of Vagrant to note:

On the host, the `kffp-website` directory is synced to the VM's `/vagrant/kffp-website` and vice-versa, so any changes made in either the VM or the host are propagated accordingly.

Lastly, edit your hosts file:
```
$ sudo nano /etc/hosts

Add the following line:

192.168.50.50  local.kffp

Save and Exit the editor:
^o to save
then hit return to accept the filename
^x to exit
```
Now you can access the site in your browser at `local.kffp`.
