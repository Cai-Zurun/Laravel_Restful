<p align="center"><img src="https://res.cloudinary.com/dtfbvvkyp/image/upload/v1566331377/laravel-logolockup-cmyk-red.svg" width="400"></p>


基于Laravel框架和RESTful设计准则，按照前后端分离的开发形式来实现简单的用户注册和登录。

（1）实现Email形式的**注册**功能和相应的**登录**功能，注册部分具备**邮件激活**功能（使用Laravel的邮件发送机制或第三方组件）；（2）实现忘记密码时通过**重置密码邮件**设置新密码（使用Laravel的邮件发送机制或第三方组件）；(发送验证码，使用redis进行存储，使用api更改密码时需填入验证码)（3）包含对某个物品（自己选择）的操作，以RESTful API风格进行；（4）使用JWT认证（JSON Web Tokens）

参考教程：https://segmentfault.com/a/1190000020433890

   ​				  https://learnku.com/articles/33735#61ca4e
