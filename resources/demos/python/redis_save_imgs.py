# -*- coding: utf-8 -*-

import sys
import os
import redis
import base64
import inspect

# 先安装扩展库 redis
# pip install redis


class Redis:

    def __init__(self, host='127.0.0.1', db=10):
        # 连接redis
        self.r = redis.Redis(host=host, port=6379, db=db)

    def __del__(self):
        print('销毁')

    def log(self, message, log_file_name='redis.log'):
        # 获取调用者的方法名
        curframe = inspect.currentframe()
        calframe = inspect.getouterframes(curframe, 2)
        caller_name = calframe[1][3]
        tool.log(message, log_file_name=log_file_name, class_name=__class__.__name__, caller_name=caller_name)

    def get_files(self, path, all_files, chunk):
        # path 绝对路径
        if not path:
            return False

        # 首先遍历当前目录所有文件及文件夹
        file_list = os.listdir(path)
        # 准备循环判断每个元素是否是文件夹还是文件，是文件的话，把名称传入list，是文件夹的话，递归
        for file in file_list:
            # print(len(all_files))
            # 满20个文件就不再取了
            if len(all_files) >= chunk:
                return all_files
            # 利用os.path.join()方法取得路径全名，并存入cur_path变量，否则每次只能遍历一层目录
            cur_path = os.path.join(path, file)
            # 判断是否是文件夹
            if os.path.isdir(cur_path):
                self.get_files(cur_path, all_files, chunk)
            else:
                all_files.append(cur_path)

        return all_files

    def save_img(self, file_name, source_path='', hase_name='test_imgs'):
        try:
            # 只接受 jpg和png 格式
            file_suffix = '.' + file_name.split('.')[-1]
            if not file_suffix in ['.png', '.jpg', '.jpeg', '.PNG', '.JPG', '.JPEG']:
                return False

            with open(file_name, 'rb') as f:
                base64_data = base64.b64encode(f.read())
                # 去除根目录，便于下载保存
                hase_key = file_name.replace(source_path, '')
                self.r.set(hase_key, base64_data)
                self.r.sadd(hase_name, hase_key)
                # 写入成功，删除本地文件
                os.remove(file_name)

        except Exception as e:
            exit(e)

    def save_imgs(self, path, hase_name='test_imgs'):
        all_files = self.get_files(path, [], 10)
        # print(all_files)
        num = 0
        for i in all_files:
            print(num, i)
            self.save_img(i, path, hase_name)
            num += 1

        self.r.save()


if __name__ == '__main__':

    r = Redis('127.0.0.1')

    # 图片转base64存redis
    r.save_imgs('/var/www/nfsqcx/public/common/clock_in_and_out_pics/', 'clock_in_and_out_pics')

    print('操作结束')
