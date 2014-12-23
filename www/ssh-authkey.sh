#!/bin/bash

script_dir=/home/www	# ssh-authkey.sh 스크립트실행위치

#local_user=nobody
local_user=root


#root계정말고 다른계정에 인증키 만들때는 아래 처럼 퍼미션을 변경해줘야함( 아래 예는  kvm 유저일경우)
#chmod 600 /home/kvm/.ssh/authorized_keys
#chmod 700 /home/kvm/.ssh


remote_user=root
#remote_host=localhost
#remote_host=192.168.2.152
remote_host="localhost"
remote_port=5135
#remote_home=/smilemrtg
target=${remote_user}@${remote_host}

saveas=./key
logdir=./key/logs	#log 쌓일 디렉토리
Identity=$saveas/id_rsa
KnownHostsFile=$saveas/known_hosts

if [ ! -d $script_dir ] ; then
	echo ""
	echo "${script_dir} 가 존재하지 않습니다."
	echo "ssh-authkey.sh 실행위치를 다시 확인하기 바랍니다."
	echo ""
	exit
fi

cd $script_dir

case "$1" in
        install)

			if [ ! -d $saveas ] ; then
				mkdir -p $saveas
				chmod 707 $saveas

				mkdir -p $logdir
				chmod 755 $logdir
				chown nobody:nobody $logdir

			fi				


			if [ ! -f $saveas/id_rsa ] ; then
        	#ssh-keygen -f id_rsa  -N "" -t rsa

				sudo -u $local_user  ssh-keygen -t rsa -f $Identity
#				chown $local_user $Identity $Identity.pub     #2010. 8. 25 길재형 추가
				chown nobody:root $saveas/*     #2010. 8. 25 길재형 추가, nobody권한을 부여해야지 웹에서 명령어 실행됨

			fi

#			local_user_home=$(grep ^${local_user}: /etc/passwd | awk -F: '{print $6}')	#확안해 보니 여기서 사용않되고 있음. 2011.3.14 길재형
# 위에 디렉토리 뽑아내는 스크립트는 에러있음. root, root1, root2 이런거 다가져오게 되어있음.... 쓰지말도록
#			if [ ! -d ${local_user_home}/.ssh ] ; then
#			    mkdir -p ${local_user_home}/.ssh	#없애도 될듯... 2011.3.14 길재형
#			fi
			
			if [ ! -d /.ssh ] ; then
			    mkdir -p /.ssh	# /.ssh 디렉토리가 없으면 cmd가 실행이 않됨(이유는 확인해봐야함)
			fi



			cat $Identity.pub | ssh -i $Identity -o UserKnownHostsFile=$KnownHostsFile  -p $remote_port ${target} 'cat >> .ssh/authorized_keys'

#			scp -i $Identity -o UserKnownHostsFile=$KnownHostsFile  id_rsa.pub ${remote_user}@${remote_host}:${remote_home}/.ssh/authorized_keys
#			scp -i $Identity -o UserKnownHostsFile=$KnownHostsFile  ../key/id_rsa.pub ${target}:${remote_home}/.ssh/authorized_keys	#윗줄의 cat명령어랑 중복되는듯..2011.3.14 길재형

        ;;


		alive)
			ssh  -i $Identity -o UserKnownHostsFile=$KnownHostsFile -o PasswordAuthentication=no  -p $remote_port ${target} 'echo alive'
		;;


		scp)
#			dst=${target}:$dst
#			scp -i $Identity -o UserKnownHostsFile=$KnownHostsFile  $src $dst
		;;


		exec)
			ssh -i $Identity -o UserKnownHostsFile=$KnownHostsFile  $target
		;;


		expire)
#			ssh -i $Identity -o UserKnownHostsFile=$KnownHostsFile    ${target} rm -f ${remote_home}/.ssh/authorized_keys
		;;


		help)
			echo "$0 [ install | alive | scp | exec | expire ]"
		;;


		clean)
			rm -f $Identity $Identity.pub
		;;
esac

function get_user_home()
{
        case "$1" in
        root)
        remote_home=/root
        ;;
        *)
        remote_home=/home/${remote_user}
        ;;
        esac
}
