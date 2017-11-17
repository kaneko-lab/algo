# Algo Game Server.
Making System 2016-2017 @ Kaneko Laboratory
Made with Cakephp 3  


## What's algo ?
>[アルゴ‐頭のよくなる推理カードゲーム](http://www.sansu-olympic.gr.jp/algo/)

## Goals
1.同一チームAI同士の対戦を支援  
2.異なるチームAI同士の対戦を支援  
3.対戦状況をウェブ上表示

## System Overall
1.初期化  
2.対戦マッチング    
3.ターンの確認  
4.自分のターンの実行  
*ゲーム終了まで3-4を反復  
 

### 1.初期化
 - Path : /Apis/initGame
 - Parameters
 -- group ID : int (事前配布)  
 -- auth : string (事前配布)  
 -- game AI name : string  
 - ex  : http://host/Apis/initGame/{group ID}/{auth}/{game AI name}.json  
 -- result : [API 詳細](https://docs.google.com/document/d/1aNj4CJs7qi0x4PnVj6vstxtcsaZd3GpOHd40OwnHZwY/edit?usp=sharing) 
 
 
### 2.対戦マッチング
 - Path : /Apis/checkMatching  
 - Parameters  
 -- group ID : int (事前配布)  
 -- auth : string (事前配布)  
 -- game ID : int
 -- game AI ID : int
 - ex  : http://host/Apis/checkMatching/{group ID}/{auth}/{game ID}/{game AI ID}.json  
 -- result : [API 詳細](https://docs.google.com/document/d/1aNj4CJs7qi0x4PnVj6vstxtcsaZd3GpOHd40OwnHZwY/edit?usp=sharing) 
 

### 3.ターンの確認
 - Path : /Apis/checkCurrentTurn  
 - Parameters  
 -- group ID : int (事前配布)  
 -- auth : string (事前配布)  
 -- game ID : int
 -- game AI ID : int
 - ex  : http://host/Apis/checkCurrentTurn/{group ID}/{auth}/{game ID}/{game AI ID}.json  
 -- result : [API 詳細](https://docs.google.com/document/d/1aNj4CJs7qi0x4PnVj6vstxtcsaZd3GpOHd40OwnHZwY/edit?usp=sharing) 
 
### 4.自分のターン実行
 - Path : /Apis/doTurnAction  
 - Parameters  
 -- group ID : int (事前配布)    
 -- auth : string (事前配布)    
 -- game ID : int  
 -- game AI ID : int  
 -- turn ID : int    
 -- action type : string (ATTACK / STAY )  
 -- attack card ID : int  
 -- target card ID : int  
 -- number : int  
 - ex  : http://host/Apis/doTurnAction/{group ID}/{auth}/{game ID}/{game AI ID}/{turn ID}/{action type}/{attack card ID}/{target card ID}/{number}.json  
 -- result : [API 詳細](https://docs.google.com/document/d/1aNj4CJs7qi0x4PnVj6vstxtcsaZd3GpOHd40OwnHZwY/edit?usp=sharing) 
 

## 対戦状況確認
 - Path : /Games  
![Alt 対戦状況](https://github.com/kaneko-lab/algo/blob/master/documents/Details.png)



